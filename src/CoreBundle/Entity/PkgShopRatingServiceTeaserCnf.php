<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopRatingServiceTeaserCnf {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null - Module instance */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance = null,
/** @var null|string - Module instance */
private ?string $cmsTplModuleInstanceId = null
, 
    // TCMSFieldNumber
/** @var int - Number of ratings to be selected */
private int $numberOfRatingsToSelectFrom = 0, 
    // TCMSFieldVarchar
/** @var string - Headline */
private string $headline = '', 
    // TCMSFieldVarchar
/** @var string - Link name for "show all" */
private string $showAllLinkName = '', 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopRatingService[] - Rating service */
private \Doctrine\Common\Collections\Collection $pkgShopRatingServiceMlt = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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



  
    // TCMSFieldNumber
public function getNumberOfRatingsToSelectFrom(): int
{
    return $this->numberOfRatingsToSelectFrom;
}
public function setNumberOfRatingsToSelectFrom(int $numberOfRatingsToSelectFrom): self
{
    $this->numberOfRatingsToSelectFrom = $numberOfRatingsToSelectFrom;

    return $this;
}


  
    // TCMSFieldVarchar
public function getHeadline(): string
{
    return $this->headline;
}
public function setHeadline(string $headline): self
{
    $this->headline = $headline;

    return $this;
}


  
    // TCMSFieldVarchar
public function getShowAllLinkName(): string
{
    return $this->showAllLinkName;
}
public function setShowAllLinkName(string $showAllLinkName): self
{
    $this->showAllLinkName = $showAllLinkName;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getPkgShopRatingServiceMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgShopRatingServiceMlt;
}
public function setPkgShopRatingServiceMlt(\Doctrine\Common\Collections\Collection $pkgShopRatingServiceMlt): self
{
    $this->pkgShopRatingServiceMlt = $pkgShopRatingServiceMlt;

    return $this;
}


  
}
