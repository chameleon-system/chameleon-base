<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopPaymentTransactionType {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** System name */
    public readonly string $systemName  ) {}
}