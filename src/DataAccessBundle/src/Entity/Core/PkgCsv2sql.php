<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

class PkgCsv2sql
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - */
        private string $name = '',
        // TCMSFieldText
        /** @var string - Column mapping */
        private string $columnMapping = '',
        // TCMSFieldVarchar
        /** @var string - File / directory */
        private string $source = '',
        // TCMSFieldVarchar
        /** @var string - Character set of the source file(s) */
        private string $sourceCharset = 'UTF-8',
        // TCMSFieldVarchar
        /** @var string - Target table */
        private string $destinationTableName = ''
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

    // TCMSFieldText
    public function getColumnMapping(): string
    {
        return $this->columnMapping;
    }

    public function setColumnMapping(string $columnMapping): self
    {
        $this->columnMapping = $columnMapping;

        return $this;
    }

    // TCMSFieldVarchar
    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    // TCMSFieldVarchar
    public function getSourceCharset(): string
    {
        return $this->sourceCharset;
    }

    public function setSourceCharset(string $sourceCharset): self
    {
        $this->sourceCharset = $sourceCharset;

        return $this;
    }

    // TCMSFieldVarchar
    public function getDestinationTableName(): string
    {
        return $this->destinationTableName;
    }

    public function setDestinationTableName(string $destinationTableName): self
    {
        $this->destinationTableName = $destinationTableName;

        return $this;
    }
}
