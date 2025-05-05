<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

class CmsLanguage
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldBoolean
        /** @var bool - Activated for frontend */
        private bool $activeForFrontEnd = true,
        // TCMSFieldVarchar
        /** @var string - ISO 639-1 language code */
        private string $iso6391 = ''
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

    // TCMSFieldVarchar
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // TCMSFieldBoolean
    public function isActiveForFrontEnd(): bool
    {
        return $this->activeForFrontEnd;
    }

    public function setActiveForFrontEnd(bool $activeForFrontEnd): self
    {
        $this->activeForFrontEnd = $activeForFrontEnd;

        return $this;
    }

    // TCMSFieldVarchar
    public function getIso6391(): string
    {
        return $this->iso6391;
    }

    public function setIso6391(string $iso6391): self
    {
        $this->iso6391 = $iso6391;

        return $this;
    }
}
