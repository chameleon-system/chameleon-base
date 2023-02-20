<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMigrationFile {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMigrationCounter|null -  */
private \ChameleonSystem\CoreBundle\Entity\CmsMigrationCounter|null $cmsMigrationCounter = null,
/** @var null|string -  */
private ?string $cmsMigrationCounterId = null
, 
    // TCMSFieldVarchar
/** @var string - Build number */
private string $buildNumber = ''  ) {}

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
public function getCmsMigrationCounter(): \ChameleonSystem\CoreBundle\Entity\CmsMigrationCounter|null
{
    return $this->cmsMigrationCounter;
}
public function setCmsMigrationCounter(\ChameleonSystem\CoreBundle\Entity\CmsMigrationCounter|null $cmsMigrationCounter): self
{
    $this->cmsMigrationCounter = $cmsMigrationCounter;
    $this->cmsMigrationCounterId = $cmsMigrationCounter?->getId();

    return $this;
}
public function getCmsMigrationCounterId(): ?string
{
    return $this->cmsMigrationCounterId;
}
public function setCmsMigrationCounterId(?string $cmsMigrationCounterId): self
{
    $this->cmsMigrationCounterId = $cmsMigrationCounterId;
    // todo - load new id
    //$this->cmsMigrationCounterId = $?->getId();

    return $this;
}



  
}
