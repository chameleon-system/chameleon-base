<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataExtranetUserAddress {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to customer */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId, 
    /** User-defined name for the address */
    public readonly string $name, 
    /** It is a DHL packing station */
    public readonly bool $isDhlPackstation, 
    /** Company */
    public readonly string $company, 
    /** USTID */
    public readonly string $vatId, 
    /** Address Appendix */
    public readonly string $addressAdditionalInfo, 
    /** Salutation */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation $dataExtranetSalutationId, 
    /** First name */
    public readonly string $firstname, 
    /** Last name */
    public readonly string $lastname, 
    /** Street */
    public readonly string $street, 
    /** Street number */
    public readonly string $streetnr, 
    /** City */
    public readonly string $city, 
    /** Zip code */
    public readonly string $postalcode, 
    /** Country */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataCountry $dataCountryId, 
    /** Telephone */
    public readonly string $telefon, 
    /** Fax */
    public readonly string $fax  ) {}
}