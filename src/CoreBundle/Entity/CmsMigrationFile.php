<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsMigrationCounter;

class CmsMigrationFile {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Build number */
private string $buildNumber = '', 
    // TCMSFieldLookup
/** @var CmsMigrationCounter|null -  */
private ?CmsMigrationCounter $cmsMigrationCounter = null
  ) {}

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
public function getBuildNumber(): string
{
    return $this->buildNumber;
}
public function setBuildNumber(string $buildNumber): self
{
    $this->buildNumber = $buildNumber;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsMigrationCounter(): ?CmsMigrationCounter
{
    return $this->cmsMigrationCounter;
}

public function setCmsMigrationCounter(?CmsMigrationCounter $cmsMigrationCounter): self
{
    $this->cmsMigrationCounter = $cmsMigrationCounter;

    return $this;
}


  
}