<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsConfigThemes {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Type */
    public readonly string $type, 
    /** Folder */
    public readonly string $directory  ) {}
}