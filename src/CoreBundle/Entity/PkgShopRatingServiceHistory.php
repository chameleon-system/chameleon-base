<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopRatingServiceHistory {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** User */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId, 
    /** Belongs to order */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrder $shopOrderId, 
    /** Date */
    public readonly \DateTime $date, 
    /** List of rating services */
    public readonly string $pkgShopRatingServiceIdList  ) {}
}