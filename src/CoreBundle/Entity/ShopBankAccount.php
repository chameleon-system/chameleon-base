<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopBankAccount {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to shop */
    public readonly \ChameleonSystem\CoreBundle\Entity\Shop $shopId, 
    /** Name */
    public readonly string $name, 
    /** Account owner */
    public readonly string $accountOwner, 
    /** Bank name */
    public readonly string $bankname, 
    /** Bank code */
    public readonly string $bankcode, 
    /** Account number */
    public readonly string $accountNumber, 
    /** BIC code */
    public readonly string $bicCode, 
    /** IBAN number */
    public readonly string $ibannumber, 
    /** Position */
    public readonly int $position  ) {}
}