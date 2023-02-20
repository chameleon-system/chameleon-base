<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgMultiModuleSetItem {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgMultiModuleSet|null - Belongs to set */
private \ChameleonSystem\CoreBundle\Entity\PkgMultiModuleSet|null $pkgMultiModuleSet = null,
/** @var null|string - Belongs to set */
private ?string $pkgMultiModuleSetId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null - Module instance */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance = null,
/** @var null|string - Module instance */
private ?string $cmsTplModuleInstanceId = null
, 
    // TCMSFieldVarchar
/** @var string - Module name */
private string $name = '', 
    // TCMSFieldPosition
/** @var int - Sorting */
private int $sortOrder = 0, 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemName = '', 
    // TCMSFieldTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Alternative link for tabs */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $alternativeTabUrlForAjax = null  ) {}

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
public function getPkgMultiModuleSet(): \ChameleonSystem\CoreBundle\Entity\PkgMultiModuleSet|null
{
    return $this->pkgMultiModuleSet;
}
public function setPkgMultiModuleSet(\ChameleonSystem\CoreBundle\Entity\PkgMultiModuleSet|null $pkgMultiModuleSet): self
{
    $this->pkgMultiModuleSet = $pkgMultiModuleSet;
    $this->pkgMultiModuleSetId = $pkgMultiModuleSet?->getId();

    return $this;
}
public function getPkgMultiModuleSetId(): ?string
{
    return $this->pkgMultiModuleSetId;
}
public function setPkgMultiModuleSetId(?string $pkgMultiModuleSetId): self
{
    $this->pkgMultiModuleSetId = $pkgMultiModuleSetId;
    // todo - load new id
    //$this->pkgMultiModuleSetId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getCmsTplModuleInstance(): \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null
{
    return $this->cmsTplModuleInstance;
}
public function setCmsTplModuleInstance(\ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance): self
{
    $this->cmsTplModuleInstance = $cmsTplModuleInstance;
    $this->cmsTplModuleInstanceId = $cmsTplModuleInstance?->getId();

    return $this;
}
public function getCmsTplModuleInstanceId(): ?string
{
    return $this->cmsTplModuleInstanceId;
}
public function setCmsTplModuleInstanceId(?string $cmsTplModuleInstanceId): self
{
    $this->cmsTplModuleInstanceId = $cmsTplModuleInstanceId;
    // todo - load new id
    //$this->cmsTplModuleInstanceId = $?->getId();

    return $this;
}



  
    // TCMSFieldPosition
public function getSortOrder(): int
{
    return $this->sortOrder;
}
public function setSortOrder(int $sortOrder): self
{
    $this->sortOrder = $sortOrder;

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


  
    // TCMSFieldTreeNode
public function getAlternativeTabUrlForAjax(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->alternativeTabUrlForAjax;
}
public function setAlternativeTabUrlForAjax(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $alternativeTabUrlForAjax): self
{
    $this->alternativeTabUrlForAjax = $alternativeTabUrlForAjax;

    return $this;
}


  
}
