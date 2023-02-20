<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataCountry {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\TCountry|null - System country */
private \ChameleonSystem\CoreBundle\Entity\TCountry|null $tCountry = null,
/** @var null|string - System country */
private ?string $tCountryId = null
, 
    // TCMSFieldBoolean
/** @var bool - Active */
private bool $active = true, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldBoolean
/** @var bool - Belongs to main group */
private bool $primaryGroup = false, 
    // TCMSFieldVarchar
/** @var string - PLZ pattern */
private string $postalcodePattern = ''  ) {}

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
    // TCMSFieldBoolean
public function isActive(): bool
{
    return $this->active;
}
public function setActive(bool $active): self
{
    $this->active = $active;

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


  
    // TCMSFieldLookup
public function getTCountry(): \ChameleonSystem\CoreBundle\Entity\TCountry|null
{
    return $this->tCountry;
}
public function setTCountry(\ChameleonSystem\CoreBundle\Entity\TCountry|null $tCountry): self
{
    $this->tCountry = $tCountry;
    $this->tCountryId = $tCountry?->getId();

    return $this;
}
public function getTCountryId(): ?string
{
    return $this->tCountryId;
}
public function setTCountryId(?string $tCountryId): self
{
    $this->tCountryId = $tCountryId;
    // todo - load new id
    //$this->tCountryId = $?->getId();

    return $this;
}



  
    // TCMSFieldBoolean
public function isPrimaryGroup(): bool
{
    return $this->primaryGroup;
}
public function setPrimaryGroup(bool $primaryGroup): self
{
    $this->primaryGroup = $primaryGroup;

    return $this;
}


  
    // TCMSFieldVarchar
public function getPostalcodePattern(): string
{
    return $this->postalcodePattern;
}
public function setPostalcodePattern(string $postalcodePattern): self
{
    $this->postalcodePattern = $postalcodePattern;

    return $this;
}


  
}
