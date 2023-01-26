<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopVariantTypeValue {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to variant type */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopVariantType $shopVariantTypeId, 
    /** Name */
    public readonly string $name, 
    /** URL name (for article link) */
    public readonly string $urlName, 
    /** Position */
    public readonly int $position, 
    /** Color value (optional) */
    public readonly string $colorCode, 
    /** Optional image or icon */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaId, 
    /** Alternative name (grouping) */
    public readonly string $nameGrouped, 
    /** Surcharge / reduction */
    public readonly float $surcharge  ) {}
}