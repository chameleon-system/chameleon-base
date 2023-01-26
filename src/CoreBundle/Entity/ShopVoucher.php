<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopVoucher {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to voucher series */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopVoucherSeries $shopVoucherSeriesId, 
    /** Code */
    public readonly string $code, 
    /** Created on */
    public readonly \DateTime $datecreated, 
    /** Used up on */
    public readonly \DateTime $dateUsedUp, 
    /** Is used up */
    public readonly bool $isUsedUp, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopVoucherUse[] Voucher usages */
    public readonly array $shopVoucherUse  ) {}
}