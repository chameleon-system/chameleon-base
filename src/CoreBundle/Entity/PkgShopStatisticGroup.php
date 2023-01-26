<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopStatisticGroup {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Field with date */
    public readonly string $dateRestrictionField, 
    /** Groups */
    public readonly string $groups, 
    /** Query */
    public readonly string $query, 
    /** Field with portal limitation */
    public readonly string $portalRestrictionField, 
    /** Name */
    public readonly string $name, 
    /** Position */
    public readonly int $position  ) {}
}