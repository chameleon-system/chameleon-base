<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopVariantSet {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopVariantType[] Variant types of variant set */
    public readonly array $shopVariantType, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsFieldConf[] Fields of variant which may differ from parent item */
    public readonly array $cmsFieldConfMlt, 
    /** Display handler for variant selection in  shop */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopVariantDisplayHandler $shopVariantDisplayHandlerId  ) {}
}