<?php

namespace Simon\Kickermanagerspiel\Domain\Repository;

use Simon\Kickermanagerspiel\Domain\Model\Demand\EffifuDemand;
use Simon\Kickermanagerspiel\Domain\Model\Player;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

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

    /**
     * @throws InvalidQueryException
     */
    public function getEffifus(EffifuDemand $effifuDemand)
    {
        $query = $this->createQuery();
        $settings = $query->getQuerySettings();
        $settings->setRespectStoragePage(false);
        $query->setQuerySettings($settings);
        $queryConstraints = [
            $query->equals('league', $effifuDemand->getLeague()),
            $query->equals('season', $effifuDemand->getSeason()),
            $query->equals('mode', $effifuDemand::MODE),
            $query->greaterThan('efficiency', 1),
        ];
        $query->matching(
            $query->logicalAnd(...$queryConstraints)
        );
        $query->setLimit($effifuDemand::ITEMSPERPAGE);
        $offset = ($effifuDemand->getPage() - 1) * $effifuDemand::ITEMSPERPAGE;
        $query->setOffset($offset);
        $query->setOrderings([
            'efficiency' => QueryInterface::ORDER_DESCENDING,
            'lastname' => QueryInterface::ORDER_ASCENDING,
        ]);

        return $query->execute();
    }
}
