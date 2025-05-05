<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

class CmsLocals
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - Date format */
        private string $dateFormat = '',
        // TCMSFieldVarchar
        /** @var string - Time format */
        private string $timeFormat = '',
        // TCMSFieldVarchar
        /** @var string - PHP local name */
        private string $phpLocalName = '',
        // TCMSFieldVarchar
        /** @var string - Short format */
        private string $dateFormatCalendar = 'DMY-',
        // TCMSFieldVarchar
        /** @var string - Numbers */
        private string $numbers = ''
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

    // TCMSFieldVarchar
    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    public function setDateFormat(string $dateFormat): self
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    // TCMSFieldVarchar
    public function getTimeFormat(): string
    {
        return $this->timeFormat;
    }

    public function setTimeFormat(string $timeFormat): self
    {
        $this->timeFormat = $timeFormat;

        return $this;
    }

    // TCMSFieldVarchar
    public function getPhpLocalName(): string
    {
        return $this->phpLocalName;
    }

    public function setPhpLocalName(string $phpLocalName): self
    {
        $this->phpLocalName = $phpLocalName;

        return $this;
    }

    // TCMSFieldVarchar
    public function getDateFormatCalendar(): string
    {
        return $this->dateFormatCalendar;
    }

    public function setDateFormatCalendar(string $dateFormatCalendar): self
    {
        $this->dateFormatCalendar = $dateFormatCalendar;

        return $this;
    }

    // TCMSFieldVarchar
    public function getNumbers(): string
    {
        return $this->numbers;
    }

    public function setNumbers(string $numbers): self
    {
        $this->numbers = $numbers;

        return $this;
    }
}
