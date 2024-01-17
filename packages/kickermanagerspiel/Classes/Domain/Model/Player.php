<?php

declare(strict_types=1);

namespace Simon\Kickermanagerspiel\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Player extends AbstractEntity
{
    protected string $title = '';
    protected string $id = '';
    protected string $mode = '';
    protected string $firstname = '';
    protected string $lastname = '';
    protected string $position = '';
    protected float $value = 0.0;
    protected ?Club $club = null;
    protected ?Club $clubBeforeFirstMatchday = null;
    protected int $points = 0;
    protected string $pointsMatchdays = '[]';
    protected int $season = 0;
    protected int $league = 0;
    protected float $efficiency = 0.0;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setPosition(string $position): void
    {
        $this->position = $position;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): void
    {
        $this->value = $value;
    }

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): void
    {
        $this->club = $club;
    }

    public function getClubBeforeFirstMatchday(): ?Club
    {
        return $this->clubBeforeFirstMatchday;
    }

    public function setClubBeforeFirstMatchday(?Club $clubBeforeFirstMatchday): void
    {
        $this->clubBeforeFirstMatchday = $clubBeforeFirstMatchday;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): void
    {
        $this->points = $points;
    }

    public function getPointsMatchdays(): string
    {
        return $this->pointsMatchdays;
    }

    public function setPointsMatchdays(string $pointsMatchdays): void
    {
        $this->pointsMatchdays = $pointsMatchdays;
    }

    public function getSeason(): int
    {
        return $this->season;
    }

    public function setSeason(int $season): void
    {
        $this->season = $season;
    }

    public function getLeague(): int
    {
        return $this->league;
    }

    public function setLeague(int $league): void
    {
        $this->league = $league;
    }

    public function getEfficiency(): float
    {
        return $this->efficiency;
    }

    public function setEfficiency(float $efficiency): void
    {
        $this->efficiency = $efficiency;
    }

    public function getRatio(): float
    {
        if ($this->getValue() == 0) {
            return 0;
        }
        return $this->getPoints() / $this->getValue();
    }

    public function getRatioRounded(): string
    {
        return number_format($this->getRatio(), 2, ',', '');
    }

    public function isApproximated(): bool
    {
        return (bool)preg_match('/\.\d{3,}/', (string)$this->getRatio());
    }
}
