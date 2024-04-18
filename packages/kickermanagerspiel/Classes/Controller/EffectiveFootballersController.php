<?php

declare(strict_types=1);

namespace Simon\Kickermanagerspiel\Controller;

use Psr\Http\Message\ResponseInterface;
use Simon\Kickermanagerspiel\Domain\Model\Demand\EffifuDemand;
use Simon\Kickermanagerspiel\Domain\Repository\PlayerRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;

class EffectiveFootballersController extends ActionController
{
    protected PlayerRepository $playerRepository;

    /**
     * @return ResponseInterface
     * @throws InvalidQueryException
     */
    public function indexAction(): ResponseInterface
    {
        $seasons = $this->playerRepository->getDistinctValues('season');
        $effifuDemand = GeneralUtility::makeInstance(EffifuDemand::class);
        $effifuDemand->setSeason(array_key_first($seasons));
        $assignedValues = [
            'seasons' => $seasons,
            'effifus' => $this->playerRepository->getEffifus($effifuDemand),
            'effifuDemand' => $effifuDemand,
        ];
        $this->view->assignMultiple($assignedValues);
        return $this->htmlResponse();
    }

    public function injectPlayerRepository(PlayerRepository $playerRepository): void
    {
        $this->playerRepository = $playerRepository;
    }
}
