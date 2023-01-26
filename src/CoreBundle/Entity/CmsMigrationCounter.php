<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMigrationCounter {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsMigrationFile[] Update data */
    public readonly array $cmsMigrationFile  ) {}
}