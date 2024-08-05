<?php

declare(strict_types=1);

namespace Simon\Kickermanagerspiel\Domain\Model\Demand;

use Simon\Kickermanagerspiel\Domain\Model\Player;
use Simon\Kickermanagerspiel\Domain\Repository\PerfectteamRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PerfectDemand extends AbstractDemand
{
    protected ?PerfectteamRepository $perfectTeamRepository = null;

    protected int $matchday = 0;
    protected bool $onlyMatchday = false;

    public function __construct()
    {
        $this->perfectTeamRepository = GeneralUtility::makeInstance(PerfectteamRepository::class);
    }

    public function getMatchday(): int
    {
        return $this->matchday;
    }

    public function setMatchday(int $matchday): void
    {
        $this->matchday = $matchday;
    }

    public function isOnlyMatchday(): bool
    {
        return $this->onlyMatchday;
    }

    public function setOnlyMatchday(bool $onlyMatchday): void
    {
        $this->onlyMatchday = $onlyMatchday;
    }

    public function createLeanPlayerArray(array $players): array
    {
        $leanArray = [
            'goalkeeper' => [],
            'defender' => [],
            'midfielder' => [],
            'forward' => [],
        ];
        foreach ($players as $key => $playersInTheirPosition) {
            /** @var Player $player */
            foreach ($playersInTheirPosition as $player) {
                $value = $player->getValue();
                $points = $this->isOnlyMatchday() ? $this->getPointsOfMatchday($player) : $player->getPoints();
                $leanArray[$key][] = [
                    'value' => $value,
                    'points' => $points,
                    'ratio' => floatval($points / $value),
                ];
            }
        }
        return $leanArray;
    }

    protected function getPointsOfMatchday(Player $player): int
    {
        $pointsMatchdaysArray = json_decode($player->getPointsMatchdays());
        return $pointsMatchdaysArray[$this->getMatchday()];
    }
}
