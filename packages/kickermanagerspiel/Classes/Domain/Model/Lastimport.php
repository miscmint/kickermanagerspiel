<?php

declare(strict_types=1);

namespace Simon\Kickermanagerspiel\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Lastimport extends AbstractEntity
{
    protected string $hash = '';
    protected int $matchday = 0;
    protected string $content = '[]';
    protected string $arraykey = '';

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    public function getMatchday(): int
    {
        return $this->matchday;
    }

    public function setMatchday(int $matchday): void
    {
        $this->matchday = $matchday;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getArraykey(): string
    {
        return $this->arraykey;
    }

    public function setArraykey(string $arraykey): void
    {
        $this->arraykey = $arraykey;
    }
}
