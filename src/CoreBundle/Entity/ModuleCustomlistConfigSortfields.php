<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ModuleCustomlistConfig;

class ModuleCustomlistConfigSortfields {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var ModuleCustomlistConfig|null - Belongs to list */
private ?ModuleCustomlistConfig $moduleCustomlistConfig = null
, 
    // TCMSFieldVarchar
/** @var string - Field name */
private string $name = ''  ) {}

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
public function getModuleCustomlistConfig(): ?ModuleCustomlistConfig
{
    return $this->moduleCustomlistConfig;
}

public function setModuleCustomlistConfig(?ModuleCustomlistConfig $moduleCustomlistConfig): self
{
    $this->moduleCustomlistConfig = $moduleCustomlistConfig;

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


  
}
