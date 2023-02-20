<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopListfilterModuleConfig {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null - Belongs to module instance */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance = null,
/** @var null|string - Belongs to module instance */
private ?string $cmsTplModuleInstanceId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null -  */
private \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null $pkgShopListfilter = null,
/** @var null|string -  */
private ?string $pkgShopListfilterId = null
, 
    // TCMSFieldText
/** @var string - Filter parameters */
private string $filterParameter = ''  ) {}

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



  
    // TCMSFieldLookup
public function getPkgShopListfilter(): \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null
{
    return $this->pkgShopListfilter;
}
public function setPkgShopListfilter(\ChameleonSystem\CoreBundle\Entity\PkgShopListfilter|null $pkgShopListfilter): self
{
    $this->pkgShopListfilter = $pkgShopListfilter;
    $this->pkgShopListfilterId = $pkgShopListfilter?->getId();

    return $this;
}
public function getPkgShopListfilterId(): ?string
{
    return $this->pkgShopListfilterId;
}
public function setPkgShopListfilterId(?string $pkgShopListfilterId): self
{
    $this->pkgShopListfilterId = $pkgShopListfilterId;
    // todo - load new id
    //$this->pkgShopListfilterId = $?->getId();

    return $this;
}



  
    // TCMSFieldText
public function getFilterParameter(): string
{
    return $this->filterParameter;
}
public function setFilterParameter(string $filterParameter): self
{
    $this->filterParameter = $filterParameter;

    return $this;
}


  
}
