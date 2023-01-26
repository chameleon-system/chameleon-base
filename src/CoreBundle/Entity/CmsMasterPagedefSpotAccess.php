<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMasterPagedefSpotAccess {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to cms page template spot */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpot $cmsMasterPagedefSpotId, 
    /** Module */
    public readonly string $model, 
    /** Views */
    public readonly string $views  ) {}
}