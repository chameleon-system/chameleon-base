<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopPaymentTransaction {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to order */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrder $shopOrderId, 
    /** Executed by user */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransactionPosition[] Positions */
    public readonly array $pkgShopPaymentTransactionPosition, 
    /** Transaction type */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransactionType $pkgShopPaymentTransactionTypeId, 
    /** Executed by CMS user */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsUser $cmsUserId, 
    /** Created on */
    public readonly \DateTime $datecreated, 
    /** Executed via IP */
    public readonly string $ip, 
    /** Value */
    public readonly float $amount, 
    /** Context */
    public readonly string $context, 
    /** Sequence number */
    public readonly string $sequenceNumber, 
    /** Confirmed */
    public readonly bool $confirmed, 
    /** Confirmed on */
    public readonly \DateTime $confirmedDate  ) {}
}