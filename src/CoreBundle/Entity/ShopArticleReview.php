<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopArticle;
use ChameleonSystem\CoreBundle\Entity\DataExtranetUser;

class ShopArticleReview {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var ShopArticle|null - Belongs to product */
private ?ShopArticle $shopArticle = null
, 
    // TCMSFieldLookupParentID
/** @var DataExtranetUser|null - Written by */
private ?DataExtranetUser $dataExtranetUser = null
, 
    // TCMSFieldVarchar
/** @var string - Author */
private string $authorName = '', 
    // TCMSFieldVarchar
/** @var string - Review title */
private string $title = '', 
    // TCMSFieldVarchar
/** @var string - Author's email address */
private string $authorEmail = '', 
    // TCMSFieldVarchar
/** @var string - Rating */
private string $rating = '', 
    // TCMSFieldVarchar
/** @var string - Helpful review */
private string $helpfulCount = '', 
    // TCMSFieldVarchar
/** @var string - Review is not helpful */
private string $notHelpfulCount = '', 
    // TCMSFieldVarchar
/** @var string - Action ID */
private string $actionId = '', 
    // TCMSFieldVarchar
/** @var string - IP address */
private string $userIp = ''  ) {}

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
public function getShopArticle(): ?ShopArticle
{
    return $this->shopArticle;
}

public function setShopArticle(?ShopArticle $shopArticle): self
{
    $this->shopArticle = $shopArticle;

    return $this;
}


  
    // TCMSFieldLookupParentID
public function getDataExtranetUser(): ?DataExtranetUser
{
    return $this->dataExtranetUser;
}

public function setDataExtranetUser(?DataExtranetUser $dataExtranetUser): self
{
    $this->dataExtranetUser = $dataExtranetUser;

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


  
    // TCMSFieldVarchar
public function getAuthorEmail(): string
{
    return $this->authorEmail;
}
public function setAuthorEmail(string $authorEmail): self
{
    $this->authorEmail = $authorEmail;

    return $this;
}


  
    // TCMSFieldVarchar
public function getRating(): string
{
    return $this->rating;
}
public function setRating(string $rating): self
{
    $this->rating = $rating;

    return $this;
}


  
    // TCMSFieldVarchar
public function getHelpfulCount(): string
{
    return $this->helpfulCount;
}
public function setHelpfulCount(string $helpfulCount): self
{
    $this->helpfulCount = $helpfulCount;

    return $this;
}


  
    // TCMSFieldVarchar
public function getNotHelpfulCount(): string
{
    return $this->notHelpfulCount;
}
public function setNotHelpfulCount(string $notHelpfulCount): self
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
