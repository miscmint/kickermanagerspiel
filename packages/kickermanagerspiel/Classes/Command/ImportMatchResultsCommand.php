<?php

namespace Simon\Kickermanagerspiel\Command;

use Doctrine\DBAL\Exception;
use Simon\Kickermanagerspiel\Helper\Usort;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ImportMatchResultsCommand extends Command
{
    protected static $defaultName = 'kickermanagerspiel:import:matchresults';

    public const NROFMATCHDAYS = 34;

    protected RequestFactory $requestFactory;
    protected ConnectionPool $connectionPool;

    protected array $apiUrls = [
        1 => 'https://api.openligadb.de/getmatchdata/bl1/',
        /*2 => 'https://api.openligadb.de/getmatchdata/bl2/',
        3 => 'https://api.openligadb.de/getmatchdata/bl3/',*/
    ];

    public function configure(): void
    {
        $this->setHelp('');
    }

    /**
     *
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        $this->connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
    }

    /**
     *
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->apiUrls as $key => $apiUrl) {
            $progressBar = new ProgressBar($output, $this::NROFMATCHDAYS);
            for ($matchDay = 1; $matchDay <= $this::NROFMATCHDAYS; $matchDay++) {
                $results = $this->getResults($apiUrl, $matchDay);
                $players = [];
                foreach ($results as $matchResult) {
                    $players = $this->distributePointsForEfficiency($matchResult, $players);
                }
                foreach ($players as $player) {
                    $this->addEfficiencyPointsToDb($player, $key, $matchDay);
                }
                $progressBar->advance();
            }
            $progressBar->finish();
            $output->writeln("\n\r");
        }
        return 0;
    }

    /**
     * @throws Exception
     */
    protected function addEfficiencyPointsToDb(array $player, int $league, int $matchDay): void
    {
        $playerFromDb = $this->getPlayerFromDb($player, $league, $matchDay);
        if (empty($playerFromDb)) {
            file_put_contents('/tmp/pelf.txt', print_r($matchDay, true), FILE_APPEND);
            file_put_contents('/tmp/pelf.txt', print_r($player, true), FILE_APPEND);
            // todo: error
            return;
        }

        $playerFromDb['efficiency'] += $player['points'];

        $this->connectionPool->getConnectionForTable('tx_kickermanagerspiel_domain_model_player')
            ->update(
                'tx_kickermanagerspiel_domain_model_player',
                ['efficiency' => $playerFromDb['efficiency']],
                ['uid' => $playerFromDb['uid']]
            );
    }

    /**
     * @throws Exception
     */
    protected function getPlayerFromDb(array $player, int $league, int $matchDay)
    {
        // todo: year dynamic
        $year = 2023;

        if (empty($player['name'])) {
            return null;
        }

        list($firstName, $lastName) = $this->getPlayerName($player['name']);

        if ($lastName == 'Rexhbiecaj') {
            $lastName = 'Rexhbecaj';
        }
        if ($lastName == 'Rexhbiecaj') {
            $lastName = 'Rexhbecaj';
        }
        if ($player['club'] == '1. FSV Mainz 05' && $player['name'] == 'van den Bergh') {
            $lastName = 'van den Berg';
        }
        if ($player['club'] == 'SC Freiburg' && $player['name'] == 'V. Weißhaupt') {
            $firstName = 'N';
        }
        if ($player['name'] == 'D. Ebimbe') {
            $firstName = '';
            $lastName = 'Dina Ebimbe';
        }
        if ($player['name'] == 'J.-S. Lee') {
            $firstName = 'Jae-Sung';
            $lastName = 'Lee';
        }
        if ($player['clubShort'] == 'BVB') {
            $player['clubShort'] = 'Dortmund';
        }
        if ($player['name'] == 'Alex Telles') {
            $firstName = 'Nathan';
            $lastName = 'Tella';
        }
        $playerFromDb = $this->getPlayerQuery($firstName, $lastName, $player, $league, $year, 'club');
        if (empty($playerFromDb)) {
            $playerFromDb = $this->getPlayerQuery($firstName, $lastName, $player, $league, $year, 'club_before_first_matchday');
            if (empty($playerFromDb)) {
                $playerFromDb = $this->getPlayerQueryWithLike($firstName, $lastName, $player, $league, $year, 'club');
                if (empty($playerFromDb)) {
                    $playerFromDb = $this->getPlayerQueryWithLike($firstName, $lastName, $player, $league, $year, 'club_before_first_matchday');
                    if (empty($playerFromDb)) {
                        $playerFromDb = $this->getPlayerQueryWithLike($lastName, '', $player, $league, $year, 'club');
                        if (empty($playerFromDb)) {
                            $playerFromDb = $this->getPlayerQueryWithLike($lastName, '', $player, $league, $year, 'club_before_first_matchday');
                        }
                    }
                }
            }
        }
        return $playerFromDb;
    }

    /**
     * @throws Exception
     */
    protected function getPlayerQuery(string $firstName, string $lastName, array $player, int $league, int $year, string $joinField)
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_kickermanagerspiel_domain_model_player');
        $queryBuilder->select('tx_kickermanagerspiel_domain_model_player.*')
            ->join(
                'tx_kickermanagerspiel_domain_model_player',
                'tx_kickermanagerspiel_domain_model_club',
                'c',
                $queryBuilder->expr()->eq('c.uid', $queryBuilder->quoteIdentifier('tx_kickermanagerspiel_domain_model_player.' . $joinField))
            )
            ->from('tx_kickermanagerspiel_domain_model_player')
            ->where(
                $queryBuilder->expr()->eq('tx_kickermanagerspiel_domain_model_player.lastname', $queryBuilder->createNamedParameter($lastName)),
                $queryBuilder->expr()->like('tx_kickermanagerspiel_domain_model_player.firstname', $queryBuilder->createNamedParameter($firstName . '%')),
                $queryBuilder->expr()->eq('tx_kickermanagerspiel_domain_model_player.league', $queryBuilder->createNamedParameter($league)),
                $queryBuilder->expr()->eq('tx_kickermanagerspiel_domain_model_player.season', $queryBuilder->createNamedParameter($year)),
                $queryBuilder->expr()->eq('tx_kickermanagerspiel_domain_model_player.mode', $queryBuilder->createNamedParameter('classic')),
                $queryBuilder->expr()->like('c.title', $queryBuilder->createNamedParameter('%' . $player['clubShort'] . '%')),
            );
        return $queryBuilder->executeQuery()->fetchAssociative();
    }

    /**
     * @throws Exception
     */
    protected function getPlayerQueryWithLike(string $firstName, string $lastName, array $player, int $league, int $year, string $joinField)
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_kickermanagerspiel_domain_model_player');
        $queryBuilder->select('tx_kickermanagerspiel_domain_model_player.*')
            ->join(
                'tx_kickermanagerspiel_domain_model_player',
                'tx_kickermanagerspiel_domain_model_club',
                'c',
                $queryBuilder->expr()->eq('c.uid', $queryBuilder->quoteIdentifier('tx_kickermanagerspiel_domain_model_player.' . $joinField))
            )
            ->from('tx_kickermanagerspiel_domain_model_player')
            ->where(
                $queryBuilder->expr()->like('tx_kickermanagerspiel_domain_model_player.lastname', $queryBuilder->createNamedParameter($lastName . '%')),
                $queryBuilder->expr()->like('tx_kickermanagerspiel_domain_model_player.firstname', $queryBuilder->createNamedParameter($firstName . '%')),
                $queryBuilder->expr()->eq('tx_kickermanagerspiel_domain_model_player.league', $queryBuilder->createNamedParameter($league)),
                $queryBuilder->expr()->eq('tx_kickermanagerspiel_domain_model_player.season', $queryBuilder->createNamedParameter($year)),
                $queryBuilder->expr()->eq('tx_kickermanagerspiel_domain_model_player.mode', $queryBuilder->createNamedParameter('classic')),
                $queryBuilder->expr()->like('c.title', $queryBuilder->createNamedParameter('%' . $player['clubShort'] . '%')),
            );
        return $queryBuilder->executeQuery()->fetchAssociative();
    }

    protected function getPlayerName(string $name): array
    {
        $name = trim($name);

        // if name is like V. Nachname
        $playerArray = explode('. ', $name);
        if (count($playerArray) == 2) {
            return [$playerArray[0], $playerArray[1]];
        }

        $playerArray = explode(', ', $name);
        if (count($playerArray) == 2) {
            if (str_contains($playerArray[1], '.')) {
                $playerArray[1] = str_replace('.', '', $playerArray[1]);
            }
            return [$playerArray[1], $playerArray[0]];
        }

        $playerArray = explode(' ', $name);
        if (count($playerArray) == 2) {
            return [$playerArray[0], $playerArray[1]];
        }
        return ['', $name];
    }

    protected function distributePointsForEfficiency(array $matchResult, array $players): array
    {
        // no efficiency points if game is not finished
        if ($matchResult['matchIsFinished'] != 1) {
            return $players;
        }
        $goalsTeam1 = $matchResult['matchResults'][1]['pointsTeam1'] ?? -1;
        $goalsTeam2 = $matchResult['matchResults'][1]['pointsTeam2'] ?? -1;

        // no efficiency points if no goals where scored
        if (
            min($goalsTeam1, $goalsTeam2) == -1 ||
            max($goalsTeam1, $goalsTeam2) == 0
        ) {
            return $players;
        }
        $goals = $matchResult['goals'];
        usort($goals, [Usort::class, 'compareGoalsOfAMatch']);

        $score = [0, 0];
        foreach ($goals as $goal) {
            $newScore = [$goal['scoreTeam1'], $goal['scoreTeam2']];

            // no efficiency points if the goal is an own goal
            if (!empty($goal['isOwnGoal'])) {
                $score = $newScore;
                continue;
            }
            $scoreDifference = $this->arrayDifference($newScore, $score);
            $players = $this->addPlayerWithEfficiency(
                $goal,
                $scoreDifference,
                $goalsTeam1,
                $goalsTeam2,
                $matchResult,
                $players
            );
            $score = $newScore;
        }
        return $players;
    }

    protected function addPlayerWithEfficiency(
        array $goal,
        array $scoreDifference,
        int $goalsTeam1,
        int $goalsTeam2,
        array $matchResult,
        array $players
    ): array {
        $points = 0;

        // if team1 scored
        if ($scoreDifference[0] == 1) {
            $club = $matchResult['team1'];
            $points = $this->calculateEfficiencyPointsOfGoal($goal['scoreTeam1'], $goalsTeam1, $goalsTeam2);

        // if team2 scored
        } elseif ($scoreDifference[1] == 1) {
            $club = $matchResult['team2'];
            $points = $this->calculateEfficiencyPointsOfGoal($goal['scoreTeam2'], $goalsTeam2, $goalsTeam1);
        }

        if (empty($points) || empty($club)) {
            return $players;
        }

        $players[] = [
            'name' => $goal['goalGetterName'],
            'club' => $club['teamName'],
            'clubShort' => $club['shortName'],
            'points' => $points,
        ];
        return $players;
    }

    protected function calculateEfficiencyPointsOfGoal(int $currentGoal, int $goals1, int $goals2): float
    {
        if ($goals1 < $goals2) { // the team that scored lost
            return 0;

        } elseif ($goals1 > $goals2) { // the team that scored won
            if ($currentGoal - $goals2 > 1) {
                return 0;
            }

            // if the team that lost hasn't scored the scorer of the 1:0 gets 2 points
            if ($goals2 == 0) {
                return 2;
            }
            return (float)(3 / ($goals2 + 1));
        }

        // the game ended in a draw
        return (float)(1 / $goals1);
    }

    protected function getResults(string $apiUrl, int $matchDay): array
    {
        $options = [
            'allow_redirects' => false,
        ];

        // todo: year dynamic
        $year = 2023;

        $url = $apiUrl . $year . '/' . $matchDay;
        $response = $this->requestFactory->request($url, 'GET', $options);
        $contents = $response->getBody()->getContents();
        $contentsArray = preg_split("/\r\n|\n|\r/", $contents);
        $json = $contentsArray[0];
        if (empty($json)) {
            return [];
        }
        return json_decode($json, true);
    }

    protected function arrayDifference($array1, $array2): array
    {
        if (count($array1) == 2 && count($array2) == 2) {
            return [
                (int)$array1[0] - (int)$array2[0],
                (int)$array1[1] - (int)$array2[1]
            ];
        }
        return [0, 0];
    }
}
