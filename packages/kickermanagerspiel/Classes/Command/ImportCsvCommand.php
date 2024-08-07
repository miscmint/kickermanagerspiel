<?php

namespace Simon\Kickermanagerspiel\Command;

use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ImportCsvCommand extends AbstractCommand
{
    protected RequestFactory $requestFactory;

    public function configure(): void
    {
        $this->setHelp('');
    }

    /**
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
    }

    /**
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->csvFiles as $key => $csvFile) {
            if (empty($csvFile)) {
                $progressBar = new ProgressBar($output, 1);
                $progressBar->finish();
                $output->writeln("\n\r");
                continue;
            }
            $this->importCsvFile($csvFile, $key, $output);
        }
        return 0;
    }

    /**
     * @throws Exception
     */
    protected function getLastImport(string $key): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_kickermanagerspiel_domain_model_lastimport');
        $statement = $queryBuilder->select('*')
            ->from('tx_kickermanagerspiel_domain_model_lastimport')
            ->where(
                $queryBuilder->expr()->eq('arraykey', $queryBuilder->createNamedParameter($key))
            )
            ->executeQuery();
        $return = $statement->fetchAssociative();
        if (!$return) {
            return [];
        }
        return $return;
    }

    protected function csvFileDidNotChange(string $hash, array $lastImport): bool
    {
        if (empty($lastImport['hash'])) {
            return false;
        }
        if ($lastImport['hash'] == $hash) {
            return true;
        }
        return false;
    }

    protected function majorChanges(array $contentsArray, array $lastImport): bool
    {
        if (empty($lastImport)) {
            return true;
        }
        $changedPlayerPoints = 0;
        $lastImportContentArray = json_decode($lastImport['content'], true);
        if (empty($lastImportContentArray)) {
            return true;
        }
        foreach ($contentsArray as $currentRecord) {
            $currentRecordArray = explode(';', $currentRecord);
            if (empty($currentRecordArray[0]) || count($currentRecordArray) == 1) {
                continue;
            }
            $id = $currentRecordArray[0];
            $points = $currentRecordArray[8];
            foreach ($lastImportContentArray as $lastRecord) {
                $lastRecordArray = explode(';', $lastRecord);
                if (
                    $lastRecordArray[0] == $id &&
                    $lastRecordArray[8] != $points
                ) {
                    $changedPlayerPoints++;
                    if ($changedPlayerPoints > 50) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @throws Exception
     */
    protected function importCsvFile(string $csvFile, string $key, OutputInterface $output): void
    {
        $options = [
            'allow_redirects' => false,
        ];
        $response = $this->requestFactory->request($csvFile, 'GET', $options);
        $contents = $response->getBody()->getContents();
        $contentsArray = preg_split("/\r\n|\n|\r/", $contents);
        $json = json_encode($contentsArray);
        $hash = sha1($json);

        // get last import
        $lastImport = $this->getLastImport($key);

        // if the csv file is still the same
        if ($this->csvFileDidNotChange($hash, $lastImport)) {
            $progressBar = new ProgressBar($output, 1);
            $progressBar->finish();
            $output->writeln("\n\r");
            return;
        }

        $currentImport = $this->createNewLastImport($lastImport, $hash, $json, $contentsArray, $key);

        $keyArray = explode('_', $key);
        $this->saveCsvFile($csvFile, $currentImport, $keyArray);

        $progressBar = new ProgressBar($output, count($contentsArray));

        $this->deletePlayersIfMatchdayEqualsZero((int)$currentImport['matchday'], $keyArray);

        foreach ($contentsArray as $item) {
            $data = str_getcsv($item, ';');
            if (empty($data[1]) || $data[1] == 'Vorname') {
                $progressBar->advance();
                continue;
            }

            $player = [
                'id' => $data[0],
                'mode' => $keyArray[0],
                'firstname' => $data[1],
                'lastname' => $this->setLastname($data[2], $data[1], $data[3]),
                'position' => strtolower($data[6]),
                'value' => $this->setValueInMillion((float)$data[7]),
                'club' => $this->setClub($data[5]),
                'points' => ($currentImport['matchday'] == 0) ? 0 : strtolower($data[8]),
                'season' => (int)$keyArray[2],
                'league' => (int)$keyArray[1]
            ];
            // if before the first matchday
            if ($currentImport['matchday'] == 0) {
                $player['club_before_first_matchday'] = $player['club'];
            }

            $playerFromDB = $this->getPlayer($player, $keyArray);

            if (empty($playerFromDB)) {
                $this->insertPlayer($player, $currentImport);
            } else {
                $this->updatePlayer($player, $currentImport, $playerFromDB);
            }
            $progressBar->advance();
        }

        $this->makeChangesIfBeforeSeason($keyArray);

        $progressBar->finish();
        $output->writeln("\n\r");
    }

    /**
     * @throws Exception
     */
    protected function makeChangesIfBeforeSeason($keyArray): void
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_kickermanagerspiel_domain_model_player');
        $count = $queryBuilder->count('uid')
            ->from('tx_kickermanagerspiel_domain_model_player')
            ->where(
                $queryBuilder->expr()->neq('points', $queryBuilder->createNamedParameter(0)),
                $queryBuilder->expr()->eq('mode', $queryBuilder->createNamedParameter($keyArray[0])),
                $queryBuilder->expr()->eq('league', $queryBuilder->createNamedParameter($keyArray[1])),
                $queryBuilder->expr()->eq('season', $queryBuilder->createNamedParameter($keyArray[2])),
            )
            ->executeQuery()
            ->fetchOne();
        if ($count == 0) {
            $this->connectionPool->getConnectionForTable('tx_kickermanagerspiel_domain_model_player')
                ->update(
                    'tx_kickermanagerspiel_domain_model_player',
                    ['points_matchdays' => '[]'],
                    [
                        'mode' => $keyArray[0],
                        'season' => (int)$keyArray[2],
                        'league' => (int)$keyArray[1],
                    ],
                );
        }
    }

    protected function updatePlayer(array $player, array $currentImport, array $playerFromDB): void
    {
        $player['tstamp'] = time();
        $pointsMatchdays = json_decode($playerFromDB['points_matchdays'], true);
        $pointsBefore = $playerFromDB['points'];
        $points = $player['points'];
        $pointsThisMatchday = $points - $pointsBefore;
        $pointsMatchdays[$currentImport['matchday']] = $pointsThisMatchday;
        if ($currentImport['matchday'] != 0) {
            $player['points_matchdays'] = json_encode($pointsMatchdays);
        } else {
            $player['points_matchdays'] = '';
        }
        $this->connectionPool->getConnectionForTable('tx_kickermanagerspiel_domain_model_player')
            ->update(
                'tx_kickermanagerspiel_domain_model_player',
                $player,
                ['uid' => $playerFromDB['uid']],
            );
    }

    protected function insertPlayer(array $player, array $currentImport): void
    {
        $player['pid'] = $this->folder;
        $player['crdate'] = time();
        $player['tstamp'] = time();
        if ($currentImport['matchday'] != 0) {
            $player['points_matchdays'] = json_encode([$currentImport['matchday'] => $player['points']]);
        } else {
            $player['points_matchdays'] = '';
        }
        $this->connectionPool->getConnectionForTable('tx_kickermanagerspiel_domain_model_player')
            ->insert(
                'tx_kickermanagerspiel_domain_model_player',
                $player,
            );
    }

    /**
     * @throws Exception
     */
    protected function getPlayer(array $player, array $keyArray): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_kickermanagerspiel_domain_model_player');
        $statement = $queryBuilder->select('*')
            ->from('tx_kickermanagerspiel_domain_model_player')
            ->where(
                $queryBuilder->expr()->eq('id', $queryBuilder->createNamedParameter($player['id'])),
                $queryBuilder->expr()->eq('mode', $queryBuilder->createNamedParameter($keyArray[0])),
                $queryBuilder->expr()->eq('season', $queryBuilder->createNamedParameter($keyArray[2])),
                $queryBuilder->expr()->eq('league', $queryBuilder->createNamedParameter($keyArray[1])),
            )
            ->executeQuery();
        $return = $statement->fetchAssociative();
        if (!$return) {
            return [];
        }
        return $return;
    }

    /**
     * @throws Exception
     */
    protected function setClub(string $club): int
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_kickermanagerspiel_domain_model_club');
        $statement = $queryBuilder->select('*')
            ->from('tx_kickermanagerspiel_domain_model_club')
            ->where(
                $queryBuilder->expr()->eq('title', $queryBuilder->createNamedParameter($club))
            )
            ->executeQuery();
        $club = $statement->fetchAssociative();
        return $club['uid'] ?? 0;
    }

    /**
     * @param array $lastImport
     * @param string $hash
     * @param false|string $json
     * @param array|false $contentsArray
     * @param string $key
     * @return array
     */
    protected function createNewLastImport(array $lastImport, string $hash, false|string $json, array|false $contentsArray, string $key): array
    {
        $currentImport = $lastImport;
        $currentImport['hash'] = $hash;
        $currentImport['content'] = $json;
        $currentImport['arraykey'] = $key;
        $currentImport['tstamp'] = time();

        // check if enough has changed to be a new match day
        if ($this->majorChanges($contentsArray, $lastImport)) {
            if (!empty($lastImport['matchday'])) {
                $currentImport['matchday'] = $lastImport['matchday'] + 1;
            } else {
                $maxPoints = 0;
                foreach ($contentsArray as $item) {
                    $data = str_getcsv($item, ';');
                    $maxPoints = max((int)$maxPoints, (int)($data[8] ?? 0));
                }
                // if players have many points it's the points from last season => match day 0
                if ($maxPoints > 100) {
                    $currentImport['matchday'] = 0;
                } else {
                    $currentImport['matchday'] = 1;
                }
            }
        } else {
            $currentImport['matchday'] = $lastImport['matchday'];
        }

        // update/insert last import
        if (empty($currentImport['uid'])) {
            $currentImport['pid'] = $this->folder;
            $currentImport['crdate'] = time();
            $this->connectionPool->getConnectionForTable('tx_kickermanagerspiel_domain_model_lastimport')
                ->insert(
                    'tx_kickermanagerspiel_domain_model_lastimport',
                    $currentImport,
                );
        } else {
            unset($currentImport['uid']);
            $this->connectionPool->getConnectionForTable('tx_kickermanagerspiel_domain_model_lastimport')
                ->update(
                    'tx_kickermanagerspiel_domain_model_lastimport',
                    $currentImport,
                    ['uid' => $lastImport['uid']]
                );
        }
        return $currentImport;
    }

    protected function setLastname(string $lastName, string $firstName, string $shortCompleteName): string
    {
        if (strlen($lastName) > 19) {
            $lastNameToReturn = str_replace($firstName, '', $shortCompleteName);
            $lastNameToReturn = trim($lastNameToReturn);
            if (empty($lastNameToReturn)) {
                $array = explode(' ', $lastName);
                return $array[0];
            }
            return $lastNameToReturn;
        }
        return $lastName;
    }

    protected function setValueInMillion(float $value): float
    {
        return $value / 1000000;
    }

    /**
     * @param int $matchday
     * @param array $keyArray
     * @return void
     */
    protected function deletePlayersIfMatchdayEqualsZero(int $matchday, array $keyArray): void
    {
        if ($matchday == 0) {
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_kickermanagerspiel_domain_model_player');
            $queryBuilder->delete('tx_kickermanagerspiel_domain_model_player')
                ->where(
                    $queryBuilder->expr()->eq('mode', $queryBuilder->createNamedParameter((string)$keyArray[0])),
                    $queryBuilder->expr()->eq('season', $queryBuilder->createNamedParameter((string)$keyArray[2])),
                    $queryBuilder->expr()->eq('league', $queryBuilder->createNamedParameter((string)$keyArray[1])),
                );
            $queryBuilder->executeStatement();
        }
    }

    protected function saveCsvFile(string $filePath, array $currentImport, array $keyArray): void
    {
        $newFileName = time() . '.csv';
        $newFilePathParts = [
            Environment::getProjectPath() . '/_CsvDateien/' . $keyArray[2] . '/',
            '/matchday' . $currentImport['matchday'] . '/',
            '/liga' . $keyArray[1] . '/',
            $keyArray[0] . '/'
        ];
        $path = '';
        foreach ($newFilePathParts as $pathPart) {
            $path .= $pathPart;
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
        }

        $fileContents = file_get_contents($filePath);
        file_put_contents($path . '/' . $newFileName, print_r($fileContents, true));
    }
}
