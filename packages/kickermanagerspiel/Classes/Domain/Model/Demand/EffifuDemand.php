<?php

declare(strict_types=1);

namespace Simon\Kickermanagerspiel\Domain\Model\Demand;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class EffifuDemand extends AbstractEntity
{
    const MODE = 'classic';
    const ITEMSPERPAGE = 10;

    protected ?int $season = null;
    protected ?int $league = 1;
    protected int $page = 1;

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

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }
}
