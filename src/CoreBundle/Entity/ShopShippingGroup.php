<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopShippingGroup {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Shipping group handler */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopShippingGroupHandler $shopShippingGroupHandlerId, 
    /** Position */
    public readonly int $position, 
    /** Active */
    public readonly bool $active, 
    /** Active from */
    public readonly \DateTime $activeFrom, 
    /** Active until */
    public readonly \DateTime $activeTo, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser[] Restrict to following customers */
    public readonly array $dataExtranetUserMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] Restrict to following customer groups */
    public readonly array $dataExtranetGroupMlt, 
    /** VAT group */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopVat $shopVatId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopShippingType[] Shipping types */
    public readonly array $shopShippingTypeMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentMethod[] Payment methods */
    public readonly array $shopPaymentMethodMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopShippingGroup[] Is displayed only if the following shipping groups are not available */
    public readonly array $shopShippingGroupMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] Restrict to the following portals */
    public readonly array $cmsPortalMlt  ) {}
}