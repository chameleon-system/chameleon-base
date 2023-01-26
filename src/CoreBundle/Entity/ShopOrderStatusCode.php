<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderStatusCode {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Belongs to shop */
    public readonly \ChameleonSystem\CoreBundle\Entity\Shop $shopId, 
    /** Send status notification via email */
    public readonly bool $sendMailNotification, 
    /** System name / merchandise management code */
    public readonly string $systemName, 
    /** Run following transaction, if status is executed */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransactionType $pkgShopPaymentTransactionTypeId, 
    /** Email profile */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataMailProfile $dataMailProfileId, 
    /** Status text */
    public readonly string $infoText  ) {}
}