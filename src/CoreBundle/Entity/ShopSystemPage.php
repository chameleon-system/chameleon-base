<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSystemPage {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to shop */
    public readonly \ChameleonSystem\CoreBundle\Entity\Shop $shopId, 
    /** Internal system name */
    public readonly string $nameInternal, 
    /** Display name */
    public readonly string $name, 
    /** Navigation item (node) */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $cmsTreeId  ) {}
}