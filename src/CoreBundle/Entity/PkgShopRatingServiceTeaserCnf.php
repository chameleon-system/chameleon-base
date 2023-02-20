<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance;

class PkgShopRatingServiceTeaserCnf {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var CmsTplModuleInstance|null - Module instance */
private ?CmsTplModuleInstance $cmsTplModuleInstance = null
, 
    // TCMSFieldVarchar
/** @var string - Number of ratings to be selected */
private string $numberOfRatingsToSelectFrom = '', 
    // TCMSFieldVarchar
/** @var string - Headline */
private string $headline = '', 
    // TCMSFieldVarchar
/** @var string - Link name for "show all" */
private string $showAllLinkName = ''  ) {}

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
public function getNumberOfRatingsToSelectFrom(): string
{
    return $this->numberOfRatingsToSelectFrom;
}
public function setNumberOfRatingsToSelectFrom(string $numberOfRatingsToSelectFrom): self
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


  
}
