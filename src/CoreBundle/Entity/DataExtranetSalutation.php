<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataExtranetSalutation {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Position */
    public readonly int $position, 
    /** Gender */
    public readonly string $gender  ) {}
}