<?php

declare(strict_types=1);

namespace Simon\Kickermanagerspiel\Domain\Model\Demand;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class RandomDemand extends AbstractEntity
{
    public const PRICECHEAPPLAYER1 = 0.5;
    public const PRICECHEAPPLAYER2 = 0.1;
    public const PRICECHEAPPLAYER3 = 0.05;
    public const DEFAULTFORMATION = 352;
    public const MAXNUMBEROFATTEMPTS = 100000;
    public const CLASSICMONEY1 = 30;
    public const CLASSICMONEY2 = 7.5;
    public const CLASSICMONEY3 = 4;
    public const CLASSICGOALKEEPERS = 2;
    public const CLASSICDEFENDERS = 4;
    public const CLASSICMIDFIELDERS = 6;
    public const CLASSICFORWARDS = 3;
    public const CLASSICPLAYERSPERCLUB = 3;
    public const INTERACTIVEMONEY1 = 42.5;
    public const INTERACTIVEMONEY2 = 10;
    public const INTERACTIVEMONEY3 = 6;
    public const INTERACTIVEGOALKEEPERS = 3;
    public const INTERACTIVEDEFENDERS = 6;
    public const INTERACTIVEMIDFIELDERS = 8;
    public const INTERACTIVEFORWARDS = 5;
    public const INTERACTIVEPLAYERSPERCLUB = 99;

    protected ?int $season = null;
    protected ?int $league = 1;
    protected string $mode = 'classic';
    protected ?int $formation = RandomDemand::DEFAULTFORMATION;
    protected ?int $playersPerClub = RandomDemand::CLASSICPLAYERSPERCLUB;
    protected string $pointsMode = 'classic';
    protected ?int $numberGoalkeepers = RandomDemand::CLASSICGOALKEEPERS;
    protected ?int $numberDefenders = RandomDemand::CLASSICDEFENDERS;
    protected ?int $numberMidfielders = RandomDemand::CLASSICMIDFIELDERS;
    protected ?int $numberForwards = RandomDemand::CLASSICFORWARDS;
    protected ?int $numberCheapGoalkeepers = 1;
    protected ?int $numberCheapDefenders = 1;
    protected ?int $numberCheapMidfielders = 1;
    protected ?int $numberCheapForwards = 1;
    protected ?float $minInvest = RandomDemand::CLASSICMONEY1;
    protected ?float $maxInvest = RandomDemand::CLASSICMONEY1;

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
            return RandomDemand::DEFAULTFORMATION;
        }
        return $this->formation ?? RandomDemand::DEFAULTFORMATION;
    }

    public function setFormation(?int $formation): void
    {
        $this->formation = $formation;
    }

    public function getPlayersPerClub(): ?int
    {
        if ($this->getMode() == 'classic') {
            return RandomDemand::CLASSICPLAYERSPERCLUB;
        }
        if ($this->getMode() == 'interactive') {
            return RandomDemand::INTERACTIVEPLAYERSPERCLUB;
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
            return RandomDemand::CLASSICGOALKEEPERS;
        }
        if ($this->getMode() == 'interactive') {
            return RandomDemand::INTERACTIVEGOALKEEPERS;
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
            return RandomDemand::CLASSICDEFENDERS;
        }
        if ($this->getMode() == 'interactive') {
            return RandomDemand::INTERACTIVEDEFENDERS;
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
            return RandomDemand::CLASSICMIDFIELDERS;
        }
        if ($this->getMode() == 'interactive') {
            return RandomDemand::INTERACTIVEMIDFIELDERS;
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
            return RandomDemand::CLASSICFORWARDS;
        }
        if ($this->getMode() == 'interactive') {
            return RandomDemand::INTERACTIVEFORWARDS;
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
            return RandomDemand::CLASSICGOALKEEPERS;
        }
        return RandomDemand::INTERACTIVEGOALKEEPERS;
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
            return RandomDemand::CLASSICDEFENDERS;
        }
        return RandomDemand::INTERACTIVEDEFENDERS;
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
            return RandomDemand::CLASSICMIDFIELDERS;
        }
        return RandomDemand::INTERACTIVEMIDFIELDERS;
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
            return RandomDemand::CLASSICFORWARDS;
        }
        return RandomDemand::INTERACTIVEFORWARDS;
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
                        return RandomDemand::CLASSICMONEY1;
                    case 2:
                        return RandomDemand::CLASSICMONEY2;
                    case 3:
                        return RandomDemand::CLASSICMONEY3;
                }
                break;
            case 'interactive':
                switch ($this->getLeague()) {
                    case 1:
                        return RandomDemand::INTERACTIVEMONEY1;
                    case 2:
                        return RandomDemand::INTERACTIVEMONEY2;
                    case 3:
                        return RandomDemand::INTERACTIVEMONEY3;
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
                'goalkeeper' => RandomDemand::CLASSICGOALKEEPERS - $this->getNumberCheapGoalkeepers(),
                'defender' => RandomDemand::CLASSICDEFENDERS - $this->getNumberCheapDefenders(),
                'midfielder' => RandomDemand::CLASSICMIDFIELDERS - $this->getNumberCheapMidfielders(),
                'forward' => RandomDemand::CLASSICFORWARDS - $this->getNumberCheapForwards(),
            ],
            'interactive' => [
                'goalkeeper' => RandomDemand::INTERACTIVEGOALKEEPERS - $this->getNumberCheapGoalkeepers(),
                'defender' => RandomDemand::INTERACTIVEDEFENDERS - $this->getNumberCheapDefenders(),
                'midfielder' => RandomDemand::INTERACTIVEMIDFIELDERS - $this->getNumberCheapMidfielders(),
                'forward' => RandomDemand::INTERACTIVEFORWARDS - $this->getNumberCheapForwards(),
            ],
            default => [
                'goalkeeper' => ($this->getNumberGoalkeepers() ?? RandomDemand::INTERACTIVEGOALKEEPERS) - $this->getNumberCheapGoalkeepers(),
                'defender' => ($this->getNumberDefenders() ?? RandomDemand::INTERACTIVEDEFENDERS) - $this->getNumberCheapDefenders(),
                'midfielder' => ($this->getNumberMidfielders() ?? RandomDemand::INTERACTIVEMIDFIELDERS) - $this->getNumberCheapMidfielders(),
                'forward' => ($this->getNumberForwards() ?? RandomDemand::INTERACTIVEFORWARDS) - $this->getNumberCheapForwards(),
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
            2 => RandomDemand::PRICECHEAPPLAYER2,
            3 => RandomDemand::PRICECHEAPPLAYER3,
            default => RandomDemand::PRICECHEAPPLAYER1,
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
                2 => RandomDemand::CLASSICMONEY2,
                3 => RandomDemand::CLASSICMONEY3,
                default => RandomDemand::CLASSICMONEY1,
            },
            default => match ($this->getLeague()) {
                2 => RandomDemand::INTERACTIVEMONEY2,
                3 => RandomDemand::INTERACTIVEMONEY3,
                default => RandomDemand::INTERACTIVEMONEY1,
            },
        };

        $moneyToSpend = $this->getMaxInvest() ?? $moneyToSpend;
        $max = $moneyToSpend - $priceCheapPlayer * $nrOfCheapPlayers;
        $min = $this->getMinInvest() - $priceCheapPlayer * $nrOfCheapPlayers;
        return [$min, $max];
    }

    public function getMaxNumberOfAttempts(): int
    {
        return RandomDemand::MAXNUMBEROFATTEMPTS;
    }
}
