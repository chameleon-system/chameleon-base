<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopVoucherUse {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to voucher */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopVoucher $shopVoucherId, 
    /** Used on */
    public readonly \DateTime $dateUsed, 
    /** Value used up */
    public readonly float $valueUsed, 
    /** Used in this order */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrder $shopOrderId, 
    /** Value consumed in the order currency */
    public readonly float $valueUsedInOrderCurrency, 
    /** Currency in which the order was made */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopCurrency $pkgShopCurrencyId  ) {}
}