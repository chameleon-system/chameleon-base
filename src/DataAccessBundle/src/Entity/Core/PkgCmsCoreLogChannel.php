<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

class PkgCmsCoreLogChannel
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarcharUnique
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldNumber
        /** @var int - Maximum age of entries for this channel (in seconds) */
        private int $maxLogAgeInSeconds = 0
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCmsident(): ?int
    {
        return $this->cmsident;
    }

    public function setCmsident(int $cmsident): self
    {
        $this->cmsident = $cmsident;

        return $this;
    }

    // TCMSFieldVarcharUnique
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // TCMSFieldNumber
    public function getMaxLogAgeInSeconds(): int
    {
        return $this->maxLogAgeInSeconds;
    }

    public function setMaxLogAgeInSeconds(int $maxLogAgeInSeconds): self
    {
        $this->maxLogAgeInSeconds = $maxLogAgeInSeconds;

        return $this;
    }
}
