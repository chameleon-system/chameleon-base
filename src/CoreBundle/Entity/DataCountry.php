<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataCountry {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Active */
    public readonly bool $active, 
    /** Name */
    public readonly string $name, 
    /** System country */
    public readonly \ChameleonSystem\CoreBundle\Entity\TCountry $tCountryId, 
    /** Belongs to main group */
    public readonly bool $primaryGroup, 
    /** PLZ pattern */
    public readonly string $postalcodePattern  ) {}
}