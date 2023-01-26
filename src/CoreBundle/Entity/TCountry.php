<?php
namespace ChameleonSystem\CoreBundle\Entity;

class TCountry {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Wikipedia name */
    public readonly string $wikipediaName, 
    /** Name */
    public readonly string $name, 
    /** ISO Code two-digit */
    public readonly string $isoCode2, 
    /** ISO code three-digit */
    public readonly string $isoCode3, 
    /** Country code */
    public readonly string $internationalDiallingCode, 
    /** German name */
    public readonly string $germanName, 
    /** German zip code */
    public readonly string $germanPostalcode, 
    /** EU member state */
    public readonly bool $euMember, 
    /** toplevel domain */
    public readonly string $toplevelDomain, 
    /** main currency */
    public readonly string $primaryCurrencyIso4217  ) {}
}