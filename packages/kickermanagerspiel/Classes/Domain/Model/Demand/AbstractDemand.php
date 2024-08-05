<?php

declare(strict_types=1);

namespace Simon\Kickermanagerspiel\Domain\Model\Demand;

use Simon\Kickermanagerspiel\Domain\Model\Player;
use Simon\Kickermanagerspiel\Domain\Repository\PlayerRepository;
use Simon\Kickermanagerspiel\Helper\Usort;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class AbstractDemand extends AbstractEntity
{
    public const float PRICECHEAPPLAYER1 = 0.5;
    public const float PRICECHEAPPLAYER2 = 0.1;
    public const float PRICECHEAPPLAYER3 = 0.05;
    public const int DEFAULTFORMATION = 442;
    public const float CLASSICMONEY1 = 30;
    public const float CLASSICMONEY2 = 7.5;
    public const float CLASSICMONEY3 = 4;
    public const int CLASSICGOALKEEPERS = 2;
    public const int CLASSICDEFENDERS = 5;
    public const int CLASSICMIDFIELDERS = 5;
    public const int CLASSICFORWARDS = 3;
    public const int CLASSICPLAYERSPERCLUB = 3;
    public const float INTERACTIVEMONEY1 = 42.5;
    public const float INTERACTIVEMONEY2 = 10;
    public const float INTERACTIVEMONEY3 = 6;
    public const int INTERACTIVEGOALKEEPERS = 3;
    public const int INTERACTIVEDEFENDERS = 7;
    public const int INTERACTIVEMIDFIELDERS = 7;
    public const int INTERACTIVEFORWARDS = 5;
    public const int INTERACTIVEPLAYERSPERCLUB = 99;

    protected ?int $season = null;
    protected ?int $league = 1;
    protected string $mode = 'classic';
    protected ?int $formation = AbstractDemand::DEFAULTFORMATION;
    protected ?int $playersPerClub = AbstractDemand::CLASSICPLAYERSPERCLUB;
    protected string $pointsMode = 'classic';
    protected ?int $numberGoalkeepers = AbstractDemand::CLASSICGOALKEEPERS;
    protected ?int $numberDefenders = AbstractDemand::CLASSICDEFENDERS;
    protected ?int $numberMidfielders = AbstractDemand::CLASSICMIDFIELDERS;
    protected ?int $numberForwards = AbstractDemand::CLASSICFORWARDS;
    protected ?int $numberCheapGoalkeepers = 1;
    protected ?int $numberCheapDefenders = 1;
    protected ?int $numberCheapMidfielders = 1;
    protected ?int $numberCheapForwards = 1;
    protected ?float $minInvest = AbstractDemand::CLASSICMONEY1;
    protected ?float $maxInvest = AbstractDemand::CLASSICMONEY1;

    public function getSeason(): ?int
    {
        return $this->season;
    }

    public function setSeason(?int $season): void
    {
        $this->season = $season;
    }

    public function getLeague(): ?int
    {
        return $this->league;
    }

    public function setLeague(?int $league): void
    {
        $this->league = $league;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    public function getFormation(): ?int
    {
        if ($this->getMode() == 'classic') {
            return AbstractDemand::DEFAULTFORMATION;
        }
        return $this->formation ?? AbstractDemand::DEFAULTFORMATION;
    }

    public function setFormation(?int $formation): void
    {
        $this->formation = $formation;
    }

    public function getPlayersPerClub(): ?int
    {
        if ($this->getMode() == 'classic') {
            return AbstractDemand::CLASSICPLAYERSPERCLUB;
        }
        if ($this->getMode() == 'interactive') {
            return AbstractDemand::INTERACTIVEPLAYERSPERCLUB;
        }
        return max(0, $this->playersPerClub);
    }

    public function setPlayersPerClub(?int $playersPerClub): void
    {
        $this->playersPerClub = $playersPerClub;
    }

    public function getPointsMode(): string
    {
        return $this->pointsMode;
    }

    public function setPointsMode(string $pointsMode): void
    {
        $this->pointsMode = $pointsMode;
    }

    public function getNumberGoalkeepers(): ?int
    {
        if ($this->getMode() == 'classic') {
            return AbstractDemand::CLASSICGOALKEEPERS;
        }
        if ($this->getMode() == 'interactive') {
            return AbstractDemand::INTERACTIVEGOALKEEPERS;
        }
        return min(3, max(0, $this->numberGoalkeepers));
    }

    public function setNumberGoalkeepers(?int $numberGoalkeepers): void
    {
        $this->numberGoalkeepers = $numberGoalkeepers;
    }

    public function getNumberDefenders(): ?int
    {
        if ($this->getMode() == 'classic') {
            return AbstractDemand::CLASSICDEFENDERS;
        }
        if ($this->getMode() == 'interactive') {
            return AbstractDemand::INTERACTIVEDEFENDERS;
        }
        return min(6, max(0, $this->numberDefenders));
    }

    public function setNumberDefenders(?int $numberDefenders): void
    {
        $this->numberDefenders = $numberDefenders;
    }

    public function getNumberMidfielders(): ?int
    {
        if ($this->getMode() == 'classic') {
            return AbstractDemand::CLASSICMIDFIELDERS;
        }
        if ($this->getMode() == 'interactive') {
            return AbstractDemand::INTERACTIVEMIDFIELDERS;
        }
        return min(8, max(0, $this->numberMidfielders));
    }

    public function setNumberMidfielders(?int $numberMidfielders): void
    {
        $this->numberMidfielders = $numberMidfielders;
    }

    public function getNumberForwards(): ?int
    {
        if ($this->getMode() == 'classic') {
            return AbstractDemand::CLASSICFORWARDS;
        }
        if ($this->getMode() == 'interactive') {
            return AbstractDemand::INTERACTIVEFORWARDS;
        }
        return min(5, max(0, $this->numberForwards));
    }

    public function setNumberForwards(?int $numberForwards): void
    {
        $this->numberForwards = $numberForwards;
    }

    public function getNumberCheapGoalkeepers(): ?int
    {
        return max(0, $this->numberCheapGoalkeepers);
    }

    public function getMaxNumberCheapGoalkeepers(): int
    {
        if ($this->getMode() == 'classic') {
            return AbstractDemand::CLASSICGOALKEEPERS;
        }
        return AbstractDemand::INTERACTIVEGOALKEEPERS;
    }

    public function setNumberCheapGoalkeepers(?int $numberCheapGoalkeepers): void
    {
        $this->numberCheapGoalkeepers = $numberCheapGoalkeepers;
    }

    public function getNumberCheapDefenders(): ?int
    {
        return max(0, $this->numberCheapDefenders);
    }

    public function getMaxNumberCheapDefenders(): int
    {
        if ($this->getMode() == 'classic') {
            return AbstractDemand::CLASSICDEFENDERS;
        }
        return AbstractDemand::INTERACTIVEDEFENDERS;
    }

    public function setNumberCheapDefenders(?int $numberCheapDefenders): void
    {
        $this->numberCheapDefenders = $numberCheapDefenders;
    }

    public function getNumberCheapMidfielders(): ?int
    {
        return max(0, $this->numberCheapMidfielders);
    }

    public function getMaxNumberCheapMidfielders(): int
    {
        if ($this->getMode() == 'classic') {
            return AbstractDemand::CLASSICMIDFIELDERS;
        }
        return AbstractDemand::INTERACTIVEMIDFIELDERS;
    }

    public function setNumberCheapMidfielders(?int $numberCheapMidfielders): void
    {
        $this->numberCheapMidfielders = $numberCheapMidfielders;
    }

    public function getNumberCheapForwards(): ?int
    {
        return max(0, $this->numberCheapForwards);
    }

    public function getMaxNumberCheapForwards(): int
    {
        if ($this->getMode() == 'classic') {
            return AbstractDemand::CLASSICFORWARDS;
        }
        return AbstractDemand::INTERACTIVEFORWARDS;
    }

    public function setNumberCheapForwards(?int $numberCheapForwards): void
    {
        $this->numberCheapForwards = $numberCheapForwards;
    }

    public function getMinInvest(): ?float
    {
        return $this->minInvest;
    }

    public function setMinInvest(?float $minInvest): void
    {
        $this->minInvest = $minInvest;
    }

    public function getMaxInvest(): ?float
    {
        return $this->maxInvest;
    }

    public function setMaxInvest(?float $maxInvest): void
    {
        $this->maxInvest = $maxInvest;
    }

    public function getMaxValueOfInvest(): ?float
    {
        switch ($this->getMode()) {
            case 'classic':
                switch ($this->getLeague()) {
                    case 1:
                        return AbstractDemand::CLASSICMONEY1;
                    case 2:
                        return AbstractDemand::CLASSICMONEY2;
                    case 3:
                        return AbstractDemand::CLASSICMONEY3;
                }
                break;
            case 'interactive':
                switch ($this->getLeague()) {
                    case 1:
                        return AbstractDemand::INTERACTIVEMONEY1;
                    case 2:
                        return AbstractDemand::INTERACTIVEMONEY2;
                    case 3:
                        return AbstractDemand::INTERACTIVEMONEY3;
                }
                break;
        }
        return 99;
    }

    public function getStep(): ?float
    {
        if ($this->getLeague() == 3) {
            return 0.05;
        }
        return 0.1;
    }

    public function getTeamNumbers(): array
    {
        return match ($this->getMode()) {
            'classic' => [
                'goalkeeper' => AbstractDemand::CLASSICGOALKEEPERS - $this->getNumberCheapGoalkeepers(),
                'defender' => AbstractDemand::CLASSICDEFENDERS - $this->getNumberCheapDefenders(),
                'midfielder' => AbstractDemand::CLASSICMIDFIELDERS - $this->getNumberCheapMidfielders(),
                'forward' => AbstractDemand::CLASSICFORWARDS - $this->getNumberCheapForwards(),
            ],
            'interactive' => [
                'goalkeeper' => AbstractDemand::INTERACTIVEGOALKEEPERS - $this->getNumberCheapGoalkeepers(),
                'defender' => AbstractDemand::INTERACTIVEDEFENDERS - $this->getNumberCheapDefenders(),
                'midfielder' => AbstractDemand::INTERACTIVEMIDFIELDERS - $this->getNumberCheapMidfielders(),
                'forward' => AbstractDemand::INTERACTIVEFORWARDS - $this->getNumberCheapForwards(),
            ],
            default => [
                'goalkeeper' => ($this->getNumberGoalkeepers() ?? AbstractDemand::INTERACTIVEGOALKEEPERS) - $this->getNumberCheapGoalkeepers(),
                'defender' => ($this->getNumberDefenders() ?? AbstractDemand::INTERACTIVEDEFENDERS) - $this->getNumberCheapDefenders(),
                'midfielder' => ($this->getNumberMidfielders() ?? AbstractDemand::INTERACTIVEMIDFIELDERS) - $this->getNumberCheapMidfielders(),
                'forward' => ($this->getNumberForwards() ?? AbstractDemand::INTERACTIVEFORWARDS) - $this->getNumberCheapForwards(),
            ],
        };
    }

    public function getNrOfCheapPlayers(): int
    {
        return $this->getNumberCheapGoalkeepers() + $this->getNumberCheapDefenders()
            + $this->getNumberCheapMidfielders() + $this->getNumberCheapForwards();
    }

    public function getPriceCheapPlayer(): float
    {
        return match ($this->getLeague()) {
            2 => AbstractDemand::PRICECHEAPPLAYER2,
            3 => AbstractDemand::PRICECHEAPPLAYER3,
            default => AbstractDemand::PRICECHEAPPLAYER1,
        };
    }

    /**
     * @return float[]
     */
    public function getMoneyRange(): array
    {
        $nrOfCheapPlayers = $this->getNrOfCheapPlayers();
        $priceCheapPlayer = $this->getPriceCheapPlayer();

        $moneyToSpend = match ($this->getMode()) {
            'classic' => match ($this->getLeague()) {
                2 => AbstractDemand::CLASSICMONEY2,
                3 => AbstractDemand::CLASSICMONEY3,
                default => AbstractDemand::CLASSICMONEY1,
            },
            default => match ($this->getLeague()) {
                2 => AbstractDemand::INTERACTIVEMONEY2,
                3 => AbstractDemand::INTERACTIVEMONEY3,
                default => AbstractDemand::INTERACTIVEMONEY1,
            },
        };

        $moneyToSpend = $this->getMaxInvest() ?? $moneyToSpend;
        $max = $moneyToSpend - $priceCheapPlayer * $nrOfCheapPlayers;
        $min = $this->getMinInvest() - $priceCheapPlayer * $nrOfCheapPlayers;
        return [$min, $max];
    }

    public function collectAllSelectablePlayers(): array
    {
        $playerRepository = GeneralUtility::makeInstance(PlayerRepository::class);
        $constraints = [
            'season' => $this->getSeason(),
            'league' => $this->getLeague(),
            'mode' => $this->getMode() !== 'custom' ? $this->getMode() : $this->getPointsMode(),
        ];
        $constraints['position'] = 'goalkeeper';
        $players = [
            'goalkeeper' => $playerRepository->findByConstraints($constraints),
        ];
        $constraints['position'] = 'defender';
        $players['defender'] = $playerRepository->findByConstraints($constraints);

        $constraints['position'] = 'midfielder';
        $players['midfielder'] = $playerRepository->findByConstraints($constraints);

        $constraints['position'] = 'forward';
        $players['forward'] = $playerRepository->findByConstraints($constraints);

        return $players;
    }

    protected function addCheapPlayersAndPlaceholdersToTeam(array $team): array
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
            'goalkeeper' => $this->getNumberGoalkeepers() - count($team['goalkeeper']) - $this->getNumberCheapGoalkeepers(),
            'defender' => $this->getNumberDefenders() - count($team['defender']) - $this->getNumberCheapDefenders(),
            'midfielder' => $this->getNumberMidfielders() - count($team['midfielder']) - $this->getNumberCheapMidfielders(),
            'forward' => $this->getNumberForwards() - count($team['forward']) - $this->getNumberCheapForwards(),
        ];
        foreach ($nrOfPlaceholders as $key => $nrOfPlaceholder) {
            for ($i = 0; $i < $nrOfPlaceholder; $i++) {
                $placeholder = GeneralUtility::makeInstance(Player::class);
                $team[$key][] = $placeholder;
            }
        }
        $nrOfCheapPlayers = [
            'goalkeeper' => $this->getNumberCheapGoalkeepers(),
            'defender' => $this->getNumberCheapDefenders(),
            'midfielder' => $this->getNumberCheapMidfielders(),
            'forward' => $this->getNumberCheapForwards(),
        ];
        foreach ($nrOfCheapPlayers as $key => $nrOfPlaceholder) {
            for ($i = 0; $i < $nrOfPlaceholder; $i++) {
                $cheapPlayer = GeneralUtility::makeInstance(Player::class);
                $cheapPlayer->setLastname('- Platzhalter -');
                switch ($this->getLeague()) {
                    case 2:
                        $cheapPlayer->setValue(AbstractDemand::PRICECHEAPPLAYER2);
                        break;
                    case 3:
                        $cheapPlayer->setValue(AbstractDemand::PRICECHEAPPLAYER3);
                        break;
                    default:
                        $cheapPlayer->setValue(AbstractDemand::PRICECHEAPPLAYER1);
                }
                $team[$key][] = $cheapPlayer;
            }
        }
        return $team;
    }
}
