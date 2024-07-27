<?php

declare(strict_types=1);

namespace Simon\Kickermanagerspiel\Controller;

use Psr\Http\Message\ResponseInterface;
use Simon\Kickermanagerspiel\Domain\Model\Demand\RandomDemand;
use Simon\Kickermanagerspiel\Domain\Model\Player;
use Simon\Kickermanagerspiel\Domain\Repository\PlayerRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class RandomTeamController extends ActionController
{
    protected PlayerRepository $playerRepository;
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
        list($team, $this->teamValue) = $randomDemand->createRandomTeam();
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
