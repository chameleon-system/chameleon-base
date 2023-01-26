<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMasterPagedefSpotParameter {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to cms page template spot */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpot $cmsMasterPagedefSpotId, 
    /** Name */
    public readonly string $name, 
    /** Value */
    public readonly string $value  ) {}
}