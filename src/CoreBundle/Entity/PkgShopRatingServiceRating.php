<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopRatingServiceRating {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopRatingService|null - Rating service */
private \ChameleonSystem\CoreBundle\Entity\PkgShopRatingService|null $pkgShopRatingService = null,
/** @var null|string - Rating service */
private ?string $pkgShopRatingServiceId = null
, 
    // TCMSFieldVarchar
/** @var string - Remote key */
private string $remoteKey = '', 
    // TCMSFieldDecimal
/** @var float - Rating */
private float $score = 0, 
    // TCMSFieldText
/** @var string - Raw data */
private string $rawdata = '', 
    // TCMSFieldVarchar
/** @var string - User who rates */
private string $ratingUser = '', 
    // TCMSFieldText
/** @var string - Rating text */
private string $ratingText = '', 
    // TCMSFieldDateTime
/** @var \DateTime|null - Date of rating */
private \DateTime|null $ratingDate = null  ) {}

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
public function getPkgShopRatingService(): \ChameleonSystem\CoreBundle\Entity\PkgShopRatingService|null
{
    return $this->pkgShopRatingService;
}
public function setPkgShopRatingService(\ChameleonSystem\CoreBundle\Entity\PkgShopRatingService|null $pkgShopRatingService): self
{
    $this->pkgShopRatingService = $pkgShopRatingService;
    $this->pkgShopRatingServiceId = $pkgShopRatingService?->getId();

    return $this;
}
public function getPkgShopRatingServiceId(): ?string
{
    return $this->pkgShopRatingServiceId;
}
public function setPkgShopRatingServiceId(?string $pkgShopRatingServiceId): self
{
    $this->pkgShopRatingServiceId = $pkgShopRatingServiceId;
    // todo - load new id
    //$this->pkgShopRatingServiceId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getRemoteKey(): string
{
    return $this->remoteKey;
}
public function setRemoteKey(string $remoteKey): self
{
    $this->remoteKey = $remoteKey;

    return $this;
}


  
    // TCMSFieldDecimal
public function getScore(): float
{
    return $this->score;
}
public function setScore(float $score): self
{
    $this->score = $score;

    return $this;
}


  
    // TCMSFieldText
public function getRawdata(): string
{
    return $this->rawdata;
}
public function setRawdata(string $rawdata): self
{
    $this->rawdata = $rawdata;

    return $this;
}


  
    // TCMSFieldVarchar
public function getRatingUser(): string
{
    return $this->ratingUser;
}
public function setRatingUser(string $ratingUser): self
{
    $this->ratingUser = $ratingUser;

    return $this;
}


  
    // TCMSFieldText
public function getRatingText(): string
{
    return $this->ratingText;
}
public function setRatingText(string $ratingText): self
{
    $this->ratingText = $ratingText;

    return $this;
}


  
    // TCMSFieldDateTime
public function getRatingDate(): \DateTime|null
{
    return $this->ratingDate;
}
public function setRatingDate(\DateTime|null $ratingDate): self
{
    $this->ratingDate = $ratingDate;

    return $this;
}


  
}
