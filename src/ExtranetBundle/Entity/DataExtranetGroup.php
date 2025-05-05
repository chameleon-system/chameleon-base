<?php

namespace ChameleonSystem\ExtranetBundle\Entity;

class DataExtranetGroup
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldBoolean
        /** @var bool - Automatic assignment is active */
        private bool $autoAssignActive = false,
        // TCMSFieldDecimal
        /** @var string - Auto assignment from order value */
        private string $autoAssignOrderValueStart = '',
        // TCMSFieldDecimal
        /** @var string - Auto assignment up to order value */
        private string $autoAssignOrderValueEnd = ''
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
    public function isAutoAssignActive(): bool
    {
        return $this->autoAssignActive;
    }

    public function setAutoAssignActive(bool $autoAssignActive): self
    {
        $this->autoAssignActive = $autoAssignActive;

        return $this;
    }

    // TCMSFieldDecimal
    public function getAutoAssignOrderValueStart(): string
    {
        return $this->autoAssignOrderValueStart;
    }

    public function setAutoAssignOrderValueStart(string $autoAssignOrderValueStart): self
    {
        $this->autoAssignOrderValueStart = $autoAssignOrderValueStart;

        return $this;
    }

    // TCMSFieldDecimal
    public function getAutoAssignOrderValueEnd(): string
    {
        return $this->autoAssignOrderValueEnd;
    }

    public function setAutoAssignOrderValueEnd(string $autoAssignOrderValueEnd): self
    {
        $this->autoAssignOrderValueEnd = $autoAssignOrderValueEnd;

        return $this;
    }
}
