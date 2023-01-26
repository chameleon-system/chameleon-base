<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMigrationFile {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Build number */
    public readonly string $buildNumber  ) {}
}