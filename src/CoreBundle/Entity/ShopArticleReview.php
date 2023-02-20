<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleReview {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle|null - Belongs to product */
private \ChameleonSystem\CoreBundle\Entity\ShopArticle|null $shopArticle = null,
/** @var null|string - Belongs to product */
private ?string $shopArticleId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null - Written by */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser = null,
/** @var null|string - Written by */
private ?string $dataExtranetUserId = null
, 
    // TCMSFieldBoolean
/** @var bool - Published */
private bool $publish = false, 
    // TCMSFieldVarchar
/** @var string - Author */
private string $authorName = '', 
    // TCMSFieldVarchar
/** @var string - Review title */
private string $title = '', 
    // TCMSFieldEmail
/** @var string - Author's email address */
private string $authorEmail = '', 
    // TCMSFieldBoolean
/** @var bool - Send comment notification to the author */
private bool $sendCommentNotification = false, 
    // TCMSFieldNumber
/** @var int - Rating */
private int $rating = 0, 
    // TCMSFieldNumber
/** @var int - Helpful review */
private int $helpfulCount = 0, 
    // TCMSFieldNumber
/** @var int - Review is not helpful */
private int $notHelpfulCount = 0, 
    // TCMSFieldVarchar
/** @var string - Action ID */
private string $actionId = '', 
    // TCMSFieldText
/** @var string - Review */
private string $comment = '', 
    // TCMSFieldDateTime
/** @var \DateTime|null - Created on */
private \DateTime|null $datecreated = null, 
    // TCMSFieldVarchar
/** @var string - IP address */
private string $userIp = ''  ) {}

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
public function getShopArticle(): \ChameleonSystem\CoreBundle\Entity\ShopArticle|null
{
    return $this->shopArticle;
}
public function setShopArticle(\ChameleonSystem\CoreBundle\Entity\ShopArticle|null $shopArticle): self
{
    $this->shopArticle = $shopArticle;
    $this->shopArticleId = $shopArticle?->getId();

    return $this;
}
public function getShopArticleId(): ?string
{
    return $this->shopArticleId;
}
public function setShopArticleId(?string $shopArticleId): self
{
    $this->shopArticleId = $shopArticleId;
    // todo - load new id
    //$this->shopArticleId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getDataExtranetUser(): \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null
{
    return $this->dataExtranetUser;
}
public function setDataExtranetUser(\ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser): self
{
    $this->dataExtranetUser = $dataExtranetUser;
    $this->dataExtranetUserId = $dataExtranetUser?->getId();

    return $this;
}
public function getDataExtranetUserId(): ?string
{
    return $this->dataExtranetUserId;
}
public function setDataExtranetUserId(?string $dataExtranetUserId): self
{
    $this->dataExtranetUserId = $dataExtranetUserId;
    // todo - load new id
    //$this->dataExtranetUserId = $?->getId();

    return $this;
}



  
    // TCMSFieldBoolean
public function isPublish(): bool
{
    return $this->publish;
}
public function setPublish(bool $publish): self
{
    $this->publish = $publish;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAuthorName(): string
{
    return $this->authorName;
}
public function setAuthorName(string $authorName): self
{
    $this->authorName = $authorName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTitle(): string
{
    return $this->title;
}
public function setTitle(string $title): self
{
    $this->title = $title;

    return $this;
}


  
    // TCMSFieldEmail
public function getAuthorEmail(): string
{
    return $this->authorEmail;
}
public function setAuthorEmail(string $authorEmail): self
{
    $this->authorEmail = $authorEmail;

    return $this;
}


  
    // TCMSFieldBoolean
public function isSendCommentNotification(): bool
{
    return $this->sendCommentNotification;
}
public function setSendCommentNotification(bool $sendCommentNotification): self
{
    $this->sendCommentNotification = $sendCommentNotification;

    return $this;
}


  
    // TCMSFieldNumber
public function getRating(): int
{
    return $this->rating;
}
public function setRating(int $rating): self
{
    $this->rating = $rating;

    return $this;
}


  
    // TCMSFieldNumber
public function getHelpfulCount(): int
{
    return $this->helpfulCount;
}
public function setHelpfulCount(int $helpfulCount): self
{
    $this->helpfulCount = $helpfulCount;

    return $this;
}


  
    // TCMSFieldNumber
public function getNotHelpfulCount(): int
{
    return $this->notHelpfulCount;
}
public function setNotHelpfulCount(int $notHelpfulCount): self
{
    $this->notHelpfulCount = $notHelpfulCount;

    return $this;
}


  
    // TCMSFieldVarchar
public function getActionId(): string
{
    return $this->actionId;
}
public function setActionId(string $actionId): self
{
    $this->actionId = $actionId;

    return $this;
}


  
    // TCMSFieldText
public function getComment(): string
{
    return $this->comment;
}
public function setComment(string $comment): self
{
    $this->comment = $comment;

    return $this;
}


  
    // TCMSFieldDateTime
public function getDatecreated(): \DateTime|null
{
    return $this->datecreated;
}
public function setDatecreated(\DateTime|null $datecreated): self
{
    $this->datecreated = $datecreated;

    return $this;
}


  
    // TCMSFieldVarchar
public function getUserIp(): string
{
    return $this->userIp;
}
public function setUserIp(string $userIp): self
{
    $this->userIp = $userIp;

    return $this;
}


  
}
