<?php

declare(strict_types=1);

namespace Simon\Kickermanagerspiel\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Club extends AbstractEntity
{
    protected string $title = '';
    protected string $id = '';

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
}
