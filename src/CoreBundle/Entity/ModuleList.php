<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance;
use ChameleonSystem\CoreBundle\Entity\ModuleListCat;

class ModuleList {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsTplModuleInstance|null - Belongs to module */
private ?CmsTplModuleInstance $cmsTplModuleInstance = null
, 
    // TCMSFieldVarchar
/** @var string - Title */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Sub headline */
private string $subHeadline = '', 
    // TCMSFieldLookup
/** @var ModuleListCat|null - Category */
private ?ModuleListCat $moduleListCat = null
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
    // TCMSFieldLookup
public function getCmsTplModuleInstance(): ?CmsTplModuleInstance
{
    return $this->cmsTplModuleInstance;
}

public function setCmsTplModuleInstance(?CmsTplModuleInstance $cmsTplModuleInstance): self
{
    $this->cmsTplModuleInstance = $cmsTplModuleInstance;

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
public function getSubHeadline(): string
{
    return $this->subHeadline;
}
public function setSubHeadline(string $subHeadline): self
{
    $this->subHeadline = $subHeadline;

    return $this;
}


  
    // TCMSFieldLookup
public function getModuleListCat(): ?ModuleListCat
{
    return $this->moduleListCat;
}

public function setModuleListCat(?ModuleListCat $moduleListCat): self
{
    $this->moduleListCat = $moduleListCat;

    return $this;
}


  
}
