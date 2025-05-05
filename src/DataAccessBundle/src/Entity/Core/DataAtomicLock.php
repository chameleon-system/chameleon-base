<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

class DataAtomicLock
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarcharUnique
        /** @var string - */
        private string $lockkey = ''
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
    public function getLockkey(): string
    {
        return $this->lockkey;
    }

    public function setLockkey(string $lockkey): self
    {
        $this->lockkey = $lockkey;

        return $this;
    }
}
