<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\TCountry;

class DataCountry {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldLookup
/** @var TCountry|null - System country */
private ?TCountry $tCountry = null
, 
    // TCMSFieldVarchar
/** @var string - PLZ pattern */
private string $postalcodePattern = ''  ) {}

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
public function getTCountry(): ?TCountry
{
    return $this->tCountry;
}

public function setTCountry(?TCountry $tCountry): self
{
    $this->tCountry = $tCountry;

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
