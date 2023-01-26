<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopVariantType {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to variant set */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopVariantSet $shopVariantSetId, 
    /** URL name */
    public readonly string $urlName, 
    /** Sorting */
    public readonly int $position, 
    /** Image or icon for variant type (optional) */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaId, 
    /** Input type of variant values in the CMS */
    public readonly string $valueSelectType, 
    /** Order values by */
    public readonly string $shopVariantTypeValueCmsfieldname, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopVariantTypeValue[] Available variant values */
    public readonly array $shopVariantTypeValue, 
    /** Name */
    public readonly string $name, 
    /** Identifier */
    public readonly string $identifier  ) {}
}