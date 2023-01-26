<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopUserPurchasedVoucher {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to customer */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId, 
    /** Voucher */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopVoucher $shopVoucherId, 
    /** Bought on */
    public readonly \DateTime $datePurchased  ) {}
}