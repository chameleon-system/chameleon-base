<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopRatingService {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Icon */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $iconCmsMedia = null,
/** @var null|string - Icon */
private ?string $iconCmsMediaId = null
, 
    // TCMSFieldBoolean
/** @var bool - Active */
private bool $active = false, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemName = '', 
    // TCMSFieldBoolean
/** @var bool - Ratings contain HTML */
private bool $ratingsContainHtml = false, 
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
    // TCMSFieldText
/** @var string - Email text */
private string $emailText = '', 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0, 
    // TCMSFieldDecimal
/** @var float - Weighting */
private float $weight = 0, 
    // TCMSFieldNumber
/** @var int - Frequency of use */
private int $numberOfTimesUsed = 0, 
    // TCMSFieldNumber
/** @var int - Last used (calender week) */
private int $lastUsedYearWeek = 0, 
    // TCMSFieldBoolean
/** @var bool - Allow import */
private bool $allowImport = false, 
    // TCMSFieldBoolean
/** @var bool - Allow sending of emails */
private bool $allowSendingEmails = true, 
    // TCMSFieldDecimal
/** @var float - Current rating */
private float $currentRating = 0, 
    // TCMSFieldEmail
/** @var string - Email provider */
private string $serviceEmail = '', 
    // TCMSFieldDateTime
/** @var \DateTime|null - Current date of rating */
private \DateTime|null $currentRatingDate = null, 
    // TCMSFieldVarchar
/** @var string - Class */
private string $class = '', 
    // TCMSFieldVarchar
/** @var string - Subtype */
private string $classSubtype = '', 
    // TCMSFieldOption
/** @var string - Class type */
private string $classType = 'Customer'  ) {}

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
    // TCMSFieldBoolean
public function isActive(): bool
{
    return $this->active;
}
public function setActive(bool $active): self
{
    $this->active = $active;

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


  
    // TCMSFieldBoolean
public function isRatingsContainHtml(): bool
{
    return $this->ratingsContainHtml;
}
public function setRatingsContainHtml(bool $ratingsContainHtml): self
{
    $this->ratingsContainHtml = $ratingsContainHtml;

    return $this;
}


  
    // TCMSFieldLookup
public function getIconCmsMedia(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->iconCmsMedia;
}
public function setIconCmsMedia(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $iconCmsMedia): self
{
    $this->iconCmsMedia = $iconCmsMedia;
    $this->iconCmsMediaId = $iconCmsMedia?->getId();

    return $this;
}
public function getIconCmsMediaId(): ?string
{
    return $this->iconCmsMediaId;
}
public function setIconCmsMediaId(?string $iconCmsMediaId): self
{
    $this->iconCmsMediaId = $iconCmsMediaId;
    // todo - load new id
    //$this->iconCmsMediaId = $?->getId();

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


  
    // TCMSFieldText
public function getEmailText(): string
{
    return $this->emailText;
}
public function setEmailText(string $emailText): self
{
    $this->emailText = $emailText;

    return $this;
}


  
    // TCMSFieldPosition
public function getPosition(): int
{
    return $this->position;
}
public function setPosition(int $position): self
{
    $this->position = $position;

    return $this;
}


  
    // TCMSFieldDecimal
public function getWeight(): float
{
    return $this->weight;
}
public function setWeight(float $weight): self
{
    $this->weight = $weight;

    return $this;
}


  
    // TCMSFieldNumber
public function getNumberOfTimesUsed(): int
{
    return $this->numberOfTimesUsed;
}
public function setNumberOfTimesUsed(int $numberOfTimesUsed): self
{
    $this->numberOfTimesUsed = $numberOfTimesUsed;

    return $this;
}


  
    // TCMSFieldNumber
public function getLastUsedYearWeek(): int
{
    return $this->lastUsedYearWeek;
}
public function setLastUsedYearWeek(int $lastUsedYearWeek): self
{
    $this->lastUsedYearWeek = $lastUsedYearWeek;

    return $this;
}


  
    // TCMSFieldBoolean
public function isAllowImport(): bool
{
    return $this->allowImport;
}
public function setAllowImport(bool $allowImport): self
{
    $this->allowImport = $allowImport;

    return $this;
}


  
    // TCMSFieldBoolean
public function isAllowSendingEmails(): bool
{
    return $this->allowSendingEmails;
}
public function setAllowSendingEmails(bool $allowSendingEmails): self
{
    $this->allowSendingEmails = $allowSendingEmails;

    return $this;
}


  
    // TCMSFieldDecimal
public function getCurrentRating(): float
{
    return $this->currentRating;
}
public function setCurrentRating(float $currentRating): self
{
    $this->currentRating = $currentRating;

    return $this;
}


  
    // TCMSFieldEmail
public function getServiceEmail(): string
{
    return $this->serviceEmail;
}
public function setServiceEmail(string $serviceEmail): self
{
    $this->serviceEmail = $serviceEmail;

    return $this;
}


  
    // TCMSFieldDateTime
public function getCurrentRatingDate(): \DateTime|null
{
    return $this->currentRatingDate;
}
public function setCurrentRatingDate(\DateTime|null $currentRatingDate): self
{
    $this->currentRatingDate = $currentRatingDate;

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


  
    // TCMSFieldOption
public function getClassType(): string
{
    return $this->classType;
}
public function setClassType(string $classType): self
{
    $this->classType = $classType;

    return $this;
}


  
}
