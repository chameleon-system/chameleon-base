<?php

namespace ChameleonSystem\CoreBundle\DataModel;

class LogViewerItemDataModel
{
    private string $filename;
    private string $size;
    private string $modified;

    public function __construct(
        string $filename,
        string $size, string
        $modified
    ) {
        $this->filename = $filename;
        $this->size = $size;
        $this->modified = $modified;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    public function getSize(): string
    {
        return $this->size;
    }

    public function setSize(string $size): void
    {
        $this->size = $size;
    }

    public function getModified(): string
    {
        return $this->modified;
    }

    public function setModified(string $modified): void
    {
        $this->modified = $modified;
    }
}