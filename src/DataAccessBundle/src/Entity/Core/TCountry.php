<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

class TCountry
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Wikipedia name */
        private string $wikipediaName = '',
        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - ISO Code two-digit */
        private string $isoCode2 = '',
        // TCMSFieldVarchar
        /** @var string - ISO code three-digit */
        private string $isoCode3 = '',
        // TCMSFieldVarchar
        /** @var string - Country code */
        private string $internationalDiallingCode = '',
        // TCMSFieldVarchar
        /** @var string - German name */
        private string $germanName = '',
        // TCMSFieldVarchar
        /** @var string - German zip code */
        private string $germanPostalcode = '',
        // TCMSFieldBoolean
        /** @var bool - EU member state */
        private bool $euMember = false,
        // TCMSFieldVarchar
        /** @var string - toplevel domain */
        private string $toplevelDomain = '',
        // TCMSFieldVarchar
        /** @var string - main currency */
        private string $primaryCurrencyIso4217 = ''
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

    // TCMSFieldVarchar
    public function getWikipediaName(): string
    {
        return $this->wikipediaName;
    }

    public function setWikipediaName(string $wikipediaName): self
    {
        $this->wikipediaName = $wikipediaName;

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

    // TCMSFieldVarchar
    public function getIsoCode2(): string
    {
        return $this->isoCode2;
    }

    public function setIsoCode2(string $isoCode2): self
    {
        $this->isoCode2 = $isoCode2;

        return $this;
    }

    // TCMSFieldVarchar
    public function getIsoCode3(): string
    {
        return $this->isoCode3;
    }

    public function setIsoCode3(string $isoCode3): self
    {
        $this->isoCode3 = $isoCode3;

        return $this;
    }

    // TCMSFieldVarchar
    public function getInternationalDiallingCode(): string
    {
        return $this->internationalDiallingCode;
    }

    public function setInternationalDiallingCode(string $internationalDiallingCode): self
    {
        $this->internationalDiallingCode = $internationalDiallingCode;

        return $this;
    }

    // TCMSFieldVarchar
    public function getGermanName(): string
    {
        return $this->germanName;
    }

    public function setGermanName(string $germanName): self
    {
        $this->germanName = $germanName;

        return $this;
    }

    // TCMSFieldVarchar
    public function getGermanPostalcode(): string
    {
        return $this->germanPostalcode;
    }

    public function setGermanPostalcode(string $germanPostalcode): self
    {
        $this->germanPostalcode = $germanPostalcode;

        return $this;
    }

    // TCMSFieldBoolean
    public function isEuMember(): bool
    {
        return $this->euMember;
    }

    public function setEuMember(bool $euMember): self
    {
        $this->euMember = $euMember;

        return $this;
    }

    // TCMSFieldVarchar
    public function getToplevelDomain(): string
    {
        return $this->toplevelDomain;
    }

    public function setToplevelDomain(string $toplevelDomain): self
    {
        $this->toplevelDomain = $toplevelDomain;

        return $this;
    }

    // TCMSFieldVarchar
    public function getPrimaryCurrencyIso4217(): string
    {
        return $this->primaryCurrencyIso4217;
    }

    public function setPrimaryCurrencyIso4217(string $primaryCurrencyIso4217): self
    {
        $this->primaryCurrencyIso4217 = $primaryCurrencyIso4217;

        return $this;
    }
}
