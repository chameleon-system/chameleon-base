<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgImageHotspot {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null - Belongs to module instance */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance = null,
/** @var null|string - Belongs to module instance */
private ?string $cmsTplModuleInstanceId = null
, 
    // TCMSFieldVarchar
/** @var string - Headline */
private string $name = '', 
    // TCMSFieldNumber
/** @var int - How long should an image be displayed (in seconds)? */
private int $autoSlideTime = 0, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItem[] - Images */
private \Doctrine\Common\Collections\Collection $pkgImageHotspotItemCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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


  
    // TCMSFieldNumber
public function getAutoSlideTime(): int
{
    return $this->autoSlideTime;
}
public function setAutoSlideTime(int $autoSlideTime): self
{
    $this->autoSlideTime = $autoSlideTime;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getPkgImageHotspotItemCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgImageHotspotItemCollection;
}
public function setPkgImageHotspotItemCollection(\Doctrine\Common\Collections\Collection $pkgImageHotspotItemCollection): self
{
    $this->pkgImageHotspotItemCollection = $pkgImageHotspotItemCollection;

    return $this;
}


  
}
