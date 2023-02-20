<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgMultiModuleModuleConfig {
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
/** @var \ChameleonSystem\CoreBundle\Entity\PkgMultiModuleSet|null - Multimodule set */
private \ChameleonSystem\CoreBundle\Entity\PkgMultiModuleSet|null $pkgMultiModuleSet = null,
/** @var null|string - Multimodule set */
private ?string $pkgMultiModuleSetId = null
  ) {}

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



  
}
