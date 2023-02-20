<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataExtranetUserAddress {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null - Belongs to customer */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser = null,
/** @var null|string - Belongs to customer */
private ?string $dataExtranetUserId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null - Salutation */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null $dataExtranetSalutation = null,
/** @var null|string - Salutation */
private ?string $dataExtranetSalutationId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataCountry|null - Country */
private \ChameleonSystem\CoreBundle\Entity\DataCountry|null $dataCountry = null,
/** @var null|string - Country */
private ?string $dataCountryId = null
, 
    // TCMSFieldVarchar
/** @var string - User-defined name for the address */
private string $name = '', 
    // TCMSFieldBoolean
/** @var bool - It is a DHL packing station */
private bool $isDhlPackstation = false, 
    // TCMSFieldVarchar
/** @var string - Company */
private string $company = '', 
    // TCMSFieldVarchar
/** @var string - USTID */
private string $vatId = '', 
    // TCMSFieldVarchar
/** @var string - Address Appendix */
private string $addressAdditionalInfo = '', 
    // TCMSFieldVarchar
/** @var string - First name */
private string $firstname = '', 
    // TCMSFieldVarchar
/** @var string - Last name */
private string $lastname = '', 
    // TCMSFieldVarchar
/** @var string - Street */
private string $street = '', 
    // TCMSFieldVarchar
/** @var string - Street number */
private string $streetnr = '', 
    // TCMSFieldVarchar
/** @var string - City */
private string $city = '', 
    // TCMSFieldVarchar
/** @var string - Zip code */
private string $postalcode = '', 
    // TCMSFieldVarchar
/** @var string - Telephone */
private string $telefon = '', 
    // TCMSFieldVarchar
/** @var string - Fax */
private string $fax = ''  ) {}

  public function getId(): ?string
  {
    return $this->id;
  }
  public function setId(string $id): self
  {
    $this->id = $id;
    return $this;
  }

  public function getCmsident(): ?int
  {
    return $this->cmsident;
  }
  public function setCmsident(int $cmsident): self
  {
    $this->cmsident = $cmsident;
    return $this;
  }
    // TCMSFieldLookup
public function getDataExtranetUser(): \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null
{
    return $this->dataExtranetUser;
}
public function setDataExtranetUser(\ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser): self
{
    $this->dataExtranetUser = $dataExtranetUser;
    $this->dataExtranetUserId = $dataExtranetUser?->getId();

    return $this;
}
public function getDataExtranetUserId(): ?string
{
    return $this->dataExtranetUserId;
}
public function setDataExtranetUserId(?string $dataExtranetUserId): self
{
    $this->dataExtranetUserId = $dataExtranetUserId;
    // todo - load new id
    //$this->dataExtranetUserId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getName(): string
{
    return $this->name;
}
public function setName(string $name): self
{
    $this->name = $name;

    return $this;
}


  
    // TCMSFieldBoolean
public function isIsDhlPackstation(): bool
{
    return $this->isDhlPackstation;
}
public function setIsDhlPackstation(bool $isDhlPackstation): self
{
    $this->isDhlPackstation = $isDhlPackstation;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCompany(): string
{
    return $this->company;
}
public function setCompany(string $company): self
{
    $this->company = $company;

    return $this;
}


  
    // TCMSFieldVarchar
public function getVatId(): string
{
    return $this->vatId;
}
public function setVatId(string $vatId): self
{
    $this->vatId = $vatId;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAddressAdditionalInfo(): string
{
    return $this->addressAdditionalInfo;
}
public function setAddressAdditionalInfo(string $addressAdditionalInfo): self
{
    $this->addressAdditionalInfo = $addressAdditionalInfo;

    return $this;
}


  
    // TCMSFieldLookup
public function getDataExtranetSalutation(): \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null
{
    return $this->dataExtranetSalutation;
}
public function setDataExtranetSalutation(\ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation|null $dataExtranetSalutation): self
{
    $this->dataExtranetSalutation = $dataExtranetSalutation;
    $this->dataExtranetSalutationId = $dataExtranetSalutation?->getId();

    return $this;
}
public function getDataExtranetSalutationId(): ?string
{
    return $this->dataExtranetSalutationId;
}
public function setDataExtranetSalutationId(?string $dataExtranetSalutationId): self
{
    $this->dataExtranetSalutationId = $dataExtranetSalutationId;
    // todo - load new id
    //$this->dataExtranetSalutationId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getFirstname(): string
{
    return $this->firstname;
}
public function setFirstname(string $firstname): self
{
    $this->firstname = $firstname;

    return $this;
}


  
    // TCMSFieldVarchar
public function getLastname(): string
{
    return $this->lastname;
}
public function setLastname(string $lastname): self
{
    $this->lastname = $lastname;

    return $this;
}


  
    // TCMSFieldVarchar
public function getStreet(): string
{
    return $this->street;
}
public function setStreet(string $street): self
{
    $this->street = $street;

    return $this;
}


  
    // TCMSFieldVarchar
public function getStreetnr(): string
{
    return $this->streetnr;
}
public function setStreetnr(string $streetnr): self
{
    $this->streetnr = $streetnr;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCity(): string
{
    return $this->city;
}
public function setCity(string $city): self
{
    $this->city = $city;

    return $this;
}


  
    // TCMSFieldVarchar
public function getPostalcode(): string
{
    return $this->postalcode;
}
public function setPostalcode(string $postalcode): self
{
    $this->postalcode = $postalcode;

    return $this;
}


  
    // TCMSFieldLookup
public function getDataCountry(): \ChameleonSystem\CoreBundle\Entity\DataCountry|null
{
    return $this->dataCountry;
}
public function setDataCountry(\ChameleonSystem\CoreBundle\Entity\DataCountry|null $dataCountry): self
{
    $this->dataCountry = $dataCountry;
    $this->dataCountryId = $dataCountry?->getId();

    return $this;
}
public function getDataCountryId(): ?string
{
    return $this->dataCountryId;
}
public function setDataCountryId(?string $dataCountryId): self
{
    $this->dataCountryId = $dataCountryId;
    // todo - load new id
    //$this->dataCountryId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getTelefon(): string
{
    return $this->telefon;
}
public function setTelefon(string $telefon): self
{
    $this->telefon = $telefon;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFax(): string
{
    return $this->fax;
}
public function setFax(string $fax): self
{
    $this->fax = $fax;

    return $this;
}


  
}
