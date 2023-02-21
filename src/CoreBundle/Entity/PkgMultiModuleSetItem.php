<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgMultiModuleSet;
use ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance;

class PkgMultiModuleSetItem {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Module name */
private string $name = '', 
    // TCMSFieldLookup
/** @var PkgMultiModuleSet|null - Belongs to set */
private ?PkgMultiModuleSet $pkgMultiModuleSet = null
, 
    // TCMSFieldLookup
/** @var CmsTplModuleInstance|null - Module instance */
private ?CmsTplModuleInstance $cmsTplModuleInstance = null
, 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemName = ''  ) {}

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
public function getPkgMultiModuleSet(): ?PkgMultiModuleSet
{
    return $this->pkgMultiModuleSet;
}

public function setPkgMultiModuleSet(?PkgMultiModuleSet $pkgMultiModuleSet): self
{
    $this->pkgMultiModuleSet = $pkgMultiModuleSet;

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
public function getSystemName(): string
{
    return $this->systemName;
}
public function setSystemName(string $systemName): self
{
    $this->systemName = $systemName;

    return $this;
}


  
}
