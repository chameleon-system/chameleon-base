<?php

namespace ChameleonSystem\ExtranetBundle\Entity;

use ChameleonSystem\DataAccessBundle\Entity\Core\DataCountry;

class DataExtranetUserAddress
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var DataExtranetUser|null - Belongs to customer */
        private ?DataExtranetUser $dataExtranetUser = null,
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
        // TCMSFieldLookup
        /** @var DataExtranetSalutation|null - Salutation */
        private ?DataExtranetSalutation $dataExtranetSalutation = null,
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
        // TCMSFieldLookup
        /** @var DataCountry|null - Country */
        private ?DataCountry $dataCountry = null,
        // TCMSFieldVarchar
        /** @var string - Telephone */
        private string $telefon = '',
        // TCMSFieldVarchar
        /** @var string - Fax */
        private string $fax = ''
    ) {
    }

    public function getId(): string
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

    // TCMSFieldLookupParentID
    public function getDataExtranetUser(): ?DataExtranetUser
    {
        return $this->dataExtranetUser;
    }

    public function setDataExtranetUser(?DataExtranetUser $dataExtranetUser): self
    {
        $this->dataExtranetUser = $dataExtranetUser;

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
    public function getDataExtranetSalutation(): ?DataExtranetSalutation
    {
        return $this->dataExtranetSalutation;
    }

    public function setDataExtranetSalutation(?DataExtranetSalutation $dataExtranetSalutation): self
    {
        $this->dataExtranetSalutation = $dataExtranetSalutation;

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
    public function getDataCountry(): ?DataCountry
    {
        return $this->dataCountry;
    }

    public function setDataCountry(?DataCountry $dataCountry): self
    {
        $this->dataCountry = $dataCountry;

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
