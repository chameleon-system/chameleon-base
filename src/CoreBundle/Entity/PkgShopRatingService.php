<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsMedia;

class PkgShopRatingService {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemName = '', 
    // TCMSFieldLookup
/** @var CmsMedia|null - Icon */
private ?CmsMedia $iconCmsMedia = null
, 
    // TCMSFieldVarchar
/** @var string - Shop URL */
private string $shopUrl = '', 
    // TCMSFieldVarchar
/** @var string - Rating URL */
private string $ratingUrl = '', 
    // TCMSFieldVarchar
/** @var string - Rating API ID */
private string $ratingApiId = '', 
    // TCMSFieldVarchar
/** @var string - Affiliate value */
private string $affiliateValue = '', 
    // TCMSFieldVarchar
/** @var string - Frequency of use */
private string $numberOfTimesUsed = '', 
    // TCMSFieldVarchar
/** @var string - Last used (calender week) */
private string $lastUsedYearWeek = '', 
    // TCMSFieldVarchar
/** @var string - Email provider */
private string $serviceEmail = '', 
    // TCMSFieldVarchar
/** @var string - Class */
private string $class = '', 
    // TCMSFieldVarchar
/** @var string - Subtype */
private string $classSubtype = ''  ) {}

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


  
    // TCMSFieldLookup
public function getIconCmsMedia(): ?CmsMedia
{
    return $this->iconCmsMedia;
}

public function setIconCmsMedia(?CmsMedia $iconCmsMedia): self
{
    $this->iconCmsMedia = $iconCmsMedia;

    return $this;
}


  
    // TCMSFieldVarchar
public function getShopUrl(): string
{
    return $this->shopUrl;
}
public function setShopUrl(string $shopUrl): self
{
    $this->shopUrl = $shopUrl;

    return $this;
}


  
    // TCMSFieldVarchar
public function getRatingUrl(): string
{
    return $this->ratingUrl;
}
public function setRatingUrl(string $ratingUrl): self
{
    $this->ratingUrl = $ratingUrl;

    return $this;
}


  
    // TCMSFieldVarchar
public function getRatingApiId(): string
{
    return $this->ratingApiId;
}
public function setRatingApiId(string $ratingApiId): self
{
    $this->ratingApiId = $ratingApiId;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAffiliateValue(): string
{
    return $this->affiliateValue;
}
public function setAffiliateValue(string $affiliateValue): self
{
    $this->affiliateValue = $affiliateValue;

    return $this;
}


  
    // TCMSFieldVarchar
public function getNumberOfTimesUsed(): string
{
    return $this->numberOfTimesUsed;
}
public function setNumberOfTimesUsed(string $numberOfTimesUsed): self
{
    $this->numberOfTimesUsed = $numberOfTimesUsed;

    return $this;
}


  
    // TCMSFieldVarchar
public function getLastUsedYearWeek(): string
{
    return $this->lastUsedYearWeek;
}
public function setLastUsedYearWeek(string $lastUsedYearWeek): self
{
    $this->lastUsedYearWeek = $lastUsedYearWeek;

    return $this;
}


  
    // TCMSFieldVarchar
public function getServiceEmail(): string
{
    return $this->serviceEmail;
}
public function setServiceEmail(string $serviceEmail): self
{
    $this->serviceEmail = $serviceEmail;

    return $this;
}


  
    // TCMSFieldVarchar
public function getClass(): string
{
    return $this->class;
}
public function setClass(string $class): self
{
    $this->class = $class;

    return $this;
}


  
    // TCMSFieldVarchar
public function getClassSubtype(): string
{
    return $this->classSubtype;
}
public function setClassSubtype(string $classSubtype): self
{
    $this->classSubtype = $classSubtype;

    return $this;
}


  
}
