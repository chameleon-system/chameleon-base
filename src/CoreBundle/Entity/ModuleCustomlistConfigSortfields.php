<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ModuleCustomlistConfigSortfields {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ModuleCustomlistConfig|null - Belongs to list */
private \ChameleonSystem\CoreBundle\Entity\ModuleCustomlistConfig|null $moduleCustomlistConfig = null,
/** @var null|string - Belongs to list */
private ?string $moduleCustomlistConfigId = null
, 
    // TCMSFieldVarchar
/** @var string - Field name */
private string $name = '', 
    // TCMSFieldOption
/** @var string - Direction */
private string $direction = 'ASC', 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0  ) {}

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
public function getModuleCustomlistConfig(): \ChameleonSystem\CoreBundle\Entity\ModuleCustomlistConfig|null
{
    return $this->moduleCustomlistConfig;
}
public function setModuleCustomlistConfig(\ChameleonSystem\CoreBundle\Entity\ModuleCustomlistConfig|null $moduleCustomlistConfig): self
{
    $this->moduleCustomlistConfig = $moduleCustomlistConfig;
    $this->moduleCustomlistConfigId = $moduleCustomlistConfig?->getId();

    return $this;
}
public function getModuleCustomlistConfigId(): ?string
{
    return $this->moduleCustomlistConfigId;
}
public function setModuleCustomlistConfigId(?string $moduleCustomlistConfigId): self
{
    $this->moduleCustomlistConfigId = $moduleCustomlistConfigId;
    // todo - load new id
    //$this->moduleCustomlistConfigId = $?->getId();

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


  
    // TCMSFieldOption
public function getDirection(): string
{
    return $this->direction;
}
public function setDirection(string $direction): self
{
    $this->direction = $direction;

    return $this;
}


  
    // TCMSFieldPosition
public function getPosition(): int
{
    return $this->position;
}
public function setPosition(int $position): self
{
    $this->position = $position;

    return $this;
}


  
}
