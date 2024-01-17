<?php

declare(strict_types=1);

namespace Simon\Kickermanagerspiel\Controller;

use Psr\Http\Message\ResponseInterface;
use Simon\Kickermanagerspiel\Domain\Model\Demand\RandomDemand;
use Simon\Kickermanagerspiel\Domain\Model\Player;
use Simon\Kickermanagerspiel\Domain\Repository\PlayerRepository;
use Simon\Kickermanagerspiel\Helper\Usort;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class RandomTeamController extends ActionController
{
    protected PlayerRepository $playerRepository;
    protected array $args = [];
    protected float $teamValue = 0;

    /**
     * @return ResponseInterface
     */
    public function indexAction(): ResponseInterface
    {
        $seasons = $this->playerRepository->getDistinctValues('season');
        $randomDemand = GeneralUtility::makeInstance(RandomDemand::class);
        $randomDemand->setSeason(array_key_first($seasons));
        $assignedValues = [
            'seasons' => $seasons,
            'leagues' => $this->playerRepository->getDistinctValues('league'),
            'randomDemand' => $randomDemand,
        ];
        $this->view->assignMultiple($assignedValues);
        return $this->htmlResponse();
    }

    /**
     * @param RandomDemand|null $randomDemand
     * @return ResponseInterface
     */
    public function createAction(?RandomDemand $randomDemand = null): ResponseInterface
    {
        if (empty($randomDemand)) {
            return new ForwardResponse('index');
        }
        $team = $this->createRandomTeam($randomDemand);
        $assignedValues = [
            'team' => $team,
            'teamValue' => $this->teamValue,
            'randomDemand' => $randomDemand,
            'seasons' => $this->playerRepository->getDistinctValues('season'),
            'leagues' => $this->playerRepository->getDistinctValues('league'),
            'success' => $this->foundRandomTeam($team),
        ];
        $this->view->assignMultiple($assignedValues);
        return $this->htmlResponse();
    }

    /**
     * @param RandomDemand $randomDemand
     * @return array
     */
    protected function createRandomTeam(RandomDemand $randomDemand): array
    {
        $players = $this->gatherAllSelectablePlayers($randomDemand);
        $leanArray = $this->createLeanPlayerArray($players);
        $teamNumbers = $randomDemand->getTeamNumbers();
        $moneyRange = $randomDemand->getMoneyRange();
        $clubConstraint = $randomDemand->getPlayersPerClub();
        $maxAttempts = $randomDemand->getMaxNumberOfAttempts();

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
                $this->teamValue = $teamValue + $randomDemand->getNrOfCheapPlayers() * $randomDemand->getPriceCheapPlayer();
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
                    return $this->addCheapPlayersAndPlaceholdersToTeam($chosenTeam, $randomDemand);
                }
            }
        }
        return $this->addCheapPlayersAndPlaceholdersToTeam([], $randomDemand);
    }

    protected function addCheapPlayersAndPlaceholdersToTeam(array $team, RandomDemand $randomDemand): array
    {
        $team['goalkeeper'] = $team['goalkeeper'] ?? [];
        $team['defender'] = $team['defender'] ?? [];
        $team['midfielder'] = $team['midfielder'] ?? [];
        $team['forward'] = $team['forward'] ?? [];

        // most valuable players first
        usort($team['goalkeeper'], [Usort::class, 'comparePlayersByValue']);
        usort($team['defender'], [Usort::class, 'comparePlayersByValue']);
        usort($team['midfielder'], [Usort::class, 'comparePlayersByValue']);
        usort($team['forward'], [Usort::class, 'comparePlayersByValue']);

        $nrOfPlaceholders = [
            'goalkeeper' => $randomDemand->getNumberGoalkeepers() - count($team['goalkeeper']) - $randomDemand->getNumberCheapGoalkeepers(),
            'defender' => $randomDemand->getNumberDefenders() - count($team['defender']) - $randomDemand->getNumberCheapDefenders(),
            'midfielder' => $randomDemand->getNumberMidfielders() - count($team['midfielder']) - $randomDemand->getNumberCheapMidfielders(),
            'forward' => $randomDemand->getNumberForwards() - count($team['forward']) - $randomDemand->getNumberCheapForwards(),
        ];
        foreach ($nrOfPlaceholders as $key => $nrOfPlaceholder) {
            for ($i = 0; $i < $nrOfPlaceholder; $i++) {
                $placeholder = GeneralUtility::makeInstance(Player::class);
                $team[$key][] = $placeholder;
            }
        }
        $nrOfCheapPlayers = [
            'goalkeeper' => $randomDemand->getNumberCheapGoalkeepers(),
            'defender' => $randomDemand->getNumberCheapDefenders(),
            'midfielder' => $randomDemand->getNumberCheapMidfielders(),
            'forward' => $randomDemand->getNumberCheapForwards(),
        ];
        foreach ($nrOfCheapPlayers as $key => $nrOfPlaceholder) {
            for ($i = 0; $i < $nrOfPlaceholder; $i++) {
                $cheapPlayer = GeneralUtility::makeInstance(Player::class);
                $cheapPlayer->setLastname('- Platzhalter -');
                switch ($randomDemand->getLeague()) {
                    case 2:
                        $cheapPlayer->setValue(RandomDemand::PRICECHEAPPLAYER2);
                        break;
                    case 3:
                        $cheapPlayer->setValue(RandomDemand::PRICECHEAPPLAYER3);
                        break;
                    default:
                        $cheapPlayer->setValue(RandomDemand::PRICECHEAPPLAYER1);
                }
                $team[$key][] = $cheapPlayer;
            }
        }
        return $team;
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

    protected function gatherAllSelectablePlayers(RandomDemand $randomDemand): array
    {
        $constraints = [
            'season' => $randomDemand->getSeason(),
            'league' => $randomDemand->getLeague(),
            'mode' => $randomDemand->getMode() !== 'custom' ? $randomDemand->getMode() : $randomDemand->getPointsMode(),
        ];
        $constraints['position'] = 'goalkeeper';
        $players = [
            'goalkeeper' => $this->playerRepository->findByConstraints($constraints),
        ];
        $constraints['position'] = 'defender';
        $players['defender'] = $this->playerRepository->findByConstraints($constraints);

        $constraints['position'] = 'midfielder';
        $players['midfielder'] = $this->playerRepository->findByConstraints($constraints);

        $constraints['position'] = 'forward';
        $players['forward'] = $this->playerRepository->findByConstraints($constraints);

        return $players;
    }

    public function foundRandomTeam(array $team): bool
    {
        foreach ($team as $position) {
            /** @var Player $player */
            foreach ($position as $player) {
                if (empty($player->getLastname())) {
                    return false;
                }
            }
        }
        return true;
    }

    public function injectPlayerRepository(PlayerRepository $playerRepository): void
    {
        $this->playerRepository = $playerRepository;
    }
}
