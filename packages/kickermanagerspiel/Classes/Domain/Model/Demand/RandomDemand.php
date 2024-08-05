<?php

declare(strict_types=1);

namespace Simon\Kickermanagerspiel\Domain\Model\Demand;

use Simon\Kickermanagerspiel\Domain\Model\Player;

class RandomDemand extends AbstractDemand
{
    public const int MAXNUMBEROFATTEMPTS = 100000;

    public function getMaxNumberOfAttempts(): int
    {
        return RandomDemand::MAXNUMBEROFATTEMPTS;
    }

    /**
     * @return array
     */
    public function createRandomTeam(): array
    {
        $players = $this->collectAllSelectablePlayers();
        $leanArray = $this->createLeanPlayerArray($players);
        $teamNumbers = $this->getTeamNumbers();
        $moneyRange = $this->getMoneyRange();
        $clubConstraint = $this->getPlayersPerClub();
        $maxAttempts = $this->getMaxNumberOfAttempts();

        for ($i = 0; $i < $maxAttempts; $i++) {
            $team = [
                'goalkeeper' => ($teamNumbers['goalkeeper'] < 1) ? [] : array_rand($players['goalkeeper'], $teamNumbers['goalkeeper']),
                'defender' => ($teamNumbers['defender'] < 1) ? [] : array_rand($players['defender'], $teamNumbers['defender']),
                'midfielder' => ($teamNumbers['midfielder'] < 1) ? [] : array_rand($players['midfielder'], $teamNumbers['midfielder']),
                'forward' => ($teamNumbers['forward'] < 1) ? [] : array_rand($players['forward'], $teamNumbers['forward']),
            ];
            foreach ($team as $key => $playersInTheirPosition) {
                if (!is_array($playersInTheirPosition)) {
                    $team[$key] = [0 => $playersInTheirPosition];
                }
            }

            $teamValue = 0;
            foreach ($team as $key => $playersInTheirPosition) {
                foreach ($playersInTheirPosition as $playerKey) {
                    $teamValue += $leanArray[$key][$playerKey];
                }
            }

            // if the money spent is in the allowed range
            if ($teamValue <= $moneyRange[1] && $teamValue >= $moneyRange[0]) {
                $teamValue = $teamValue + $this->getNrOfCheapPlayers() * $this->getPriceCheapPlayer();
                $chosenTeam = [
                    'goalkeeper' => [],
                    'defender' => [],
                    'midfielder' => [],
                    'forward' => [],
                ];
                $clubs = [];
                foreach ($team as $key => $playersInTheirPosition) {
                    foreach ($playersInTheirPosition as $playerKey) {
                        /** Player */
                        $player = $players[$key][$playerKey];
                        $chosenTeam[$key][] = $player;
                        if (empty($clubs[$player->getClub()->getId()])) {
                            $clubs[$player->getClub()->getId()] = 1;
                        } else {
                            $clubs[$player->getClub()->getId()]++;
                        }
                    }
                }

                // if too many players from one club: continue searching
                if (!empty($clubConstraint)) {
                    foreach ($clubs as $club) {
                        if ($club > $clubConstraint) {
                            continue 2;
                        }
                    }
                    return [$this->addCheapPlayersAndPlaceholdersToTeam($chosenTeam), $teamValue];
                }
            }
        }
        return [$this->addCheapPlayersAndPlaceholdersToTeam([]), $teamValue ?? 0];
    }

    protected function createLeanPlayerArray(array $players): array
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
                $leanArray[$key][] = $player->getValue();
            }
        }
        return $leanArray;
    }
}
