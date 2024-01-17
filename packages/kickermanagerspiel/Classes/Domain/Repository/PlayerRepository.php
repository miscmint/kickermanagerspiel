<?php

namespace Simon\Kickermanagerspiel\Domain\Repository;

use Simon\Kickermanagerspiel\Domain\Model\Player;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class PlayerRepository extends Repository
{
    public function findByConstraints(array $constraints): array
    {
        $query = $this->createQuery();
        $settings = $query->getQuerySettings();
        $settings->setRespectStoragePage(false);
        $queryConstraints = [];
        foreach ($constraints as $key => $constraint) {
            $queryConstraints[] = $query->equals($key, $constraint);
        }
        $query->matching($query->logicalAnd(...$queryConstraints));
        return $query->execute()->toArray();
    }

    public function getDistinctValues(string $field): array
    {
        $query = $this->createQuery();
        $settings = $query->getQuerySettings();
        $settings->setRespectStoragePage(false);
        $players = $query->execute()->toArray();
        $values = [];
        /** @var Player $player */
        foreach ($players as $player) {
            switch ($field) {
                case 'season':
                    $season = $player->getSeason();
                    if (empty($values[$season])) {
                        $seasonNextYear = (int)substr($season, 2) + 1;
                        $seasonValue = $season . '/' . $seasonNextYear;
                        $values[$season] = $seasonValue;
                    }
                    krsort($values);
                    break;
                case 'league':
                    $league = $player->getLeague();
                    if (empty($values[$league])) {
                        $values[$league] = match ($league) {
                            1, 2 => $league . '. Bundesliga',
                            default => $league . '. Liga',
                        };
                    }
                    ksort($values);
                    break;
            }
        }
        return $values;
    }
}
