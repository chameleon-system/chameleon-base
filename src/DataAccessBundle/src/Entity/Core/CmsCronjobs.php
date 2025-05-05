<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

class CmsCronjobs
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldBoolean
        /** @var bool - Is running at the moment (locked) */
        private bool $lock = false,
        // TCMSFieldVarchar
        /** @var string - Last executed on */
        private string $lastExecution = '',
        // TCMSFieldDateTime
        /** @var \DateTime|null - Last excecuted (real) */
        private ?\DateTime $realLastExecution = null,
        // TCMSFieldVarchar
        /** @var string - Class name/service ID */
        private string $cronClass = '',
        // TCMSFieldOption
        /** @var string - Class type */
        private string $classLocation = 'Core',
        // TCMSFieldVarchar
        /** @var string - Class path */
        private string $classSubtype = '',
        // TCMSFieldVarchar
        /** @var string - Reset lock after N minutes */
        private string $unlockAfterNMinutes = '',
        // TCMSFieldVarchar
        /** @var string - Execute every N minutes */
        private string $executeEveryNMinutes = '',
        // TCMSFieldDate
        /** @var \DateTime|null - Active until */
        private ?\DateTime $endExecution = null,
        // TCMSFieldDate
        /** @var \DateTime|null - Active from */
        private ?\DateTime $startExecution = null,
        // TCMSFieldBoolean
        /** @var bool - Active */
        private bool $active = false,
        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = ''
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

    // TCMSFieldBoolean
    public function isLock(): bool
    {
        return $this->lock;
    }

    public function setLock(bool $lock): self
    {
        $this->lock = $lock;

        return $this;
    }

    // TCMSFieldVarchar
    public function getLastExecution(): string
    {
        return $this->lastExecution;
    }

    public function setLastExecution(string $lastExecution): self
    {
        $this->lastExecution = $lastExecution;

        return $this;
    }

    // TCMSFieldDateTime
    public function getRealLastExecution(): ?\DateTime
    {
        return $this->realLastExecution;
    }

    public function setRealLastExecution(?\DateTime $realLastExecution): self
    {
        $this->realLastExecution = $realLastExecution;

        return $this;
    }

    // TCMSFieldVarchar
    public function getCronClass(): string
    {
        return $this->cronClass;
    }

    public function setCronClass(string $cronClass): self
    {
        $this->cronClass = $cronClass;

        return $this;
    }

    // TCMSFieldOption
    public function getClassLocation(): string
    {
        return $this->classLocation;
    }

    public function setClassLocation(string $classLocation): self
    {
        $this->classLocation = $classLocation;

        return $this;
    }

    // TCMSFieldVarchar
    public function getClassSubtype(): string
    {
        return $this->classSubtype;
    }

    public function setClassSubtype(string $classSubtype): self
    {
        $this->classSubtype = $classSubtype;

        return $this;
    }

    // TCMSFieldVarchar
    public function getUnlockAfterNMinutes(): string
    {
        return $this->unlockAfterNMinutes;
    }

    public function setUnlockAfterNMinutes(string $unlockAfterNMinutes): self
    {
        $this->unlockAfterNMinutes = $unlockAfterNMinutes;

        return $this;
    }

    // TCMSFieldVarchar
    public function getExecuteEveryNMinutes(): string
    {
        return $this->executeEveryNMinutes;
    }

    public function setExecuteEveryNMinutes(string $executeEveryNMinutes): self
    {
        $this->executeEveryNMinutes = $executeEveryNMinutes;

        return $this;
    }

    // TCMSFieldDate
    public function getEndExecution(): ?\DateTime
    {
        return $this->endExecution;
    }

    public function setEndExecution(?\DateTime $endExecution): self
    {
        $this->endExecution = $endExecution;

        return $this;
    }

    // TCMSFieldDate
    public function getStartExecution(): ?\DateTime
    {
        return $this->startExecution;
    }

    public function setStartExecution(?\DateTime $startExecution): self
    {
        $this->startExecution = $startExecution;

        return $this;
    }

    // TCMSFieldBoolean
    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

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
}
