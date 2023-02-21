<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\DataExtranetUser;
use ChameleonSystem\CoreBundle\Entity\ShopArticle;

class ShopSuggestArticleLog {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var DataExtranetUser|null - Shop customer */
private ?DataExtranetUser $dataExtranetUser = null
, 
    // TCMSFieldLookup
/** @var ShopArticle|null - Product / item */
private ?ShopArticle $shopArticle = null
, 
    // TCMSFieldVarchar
/** @var string - From (email) */
private string $fromEmail = '', 
    // TCMSFieldVarchar
/** @var string - From (name) */
private string $fromName = '', 
    // TCMSFieldVarchar
/** @var string - Feedback recipient (email address) */
private string $toEmail = '', 
    // TCMSFieldVarchar
/** @var string - To (name) */
private string $toName = ''  ) {}

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
    // TCMSFieldLookup
public function getDataExtranetUser(): ?DataExtranetUser
{
    return $this->dataExtranetUser;
}

public function setDataExtranetUser(?DataExtranetUser $dataExtranetUser): self
{
    $this->dataExtranetUser = $dataExtranetUser;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopArticle(): ?ShopArticle
{
    return $this->shopArticle;
}

public function setShopArticle(?ShopArticle $shopArticle): self
{
    $this->shopArticle = $shopArticle;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFromEmail(): string
{
    return $this->fromEmail;
}
public function setFromEmail(string $fromEmail): self
{
    $this->fromEmail = $fromEmail;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFromName(): string
{
    return $this->fromName;
}
public function setFromName(string $fromName): self
{
    $this->fromName = $fromName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getToEmail(): string
{
    return $this->toEmail;
}
public function setToEmail(string $toEmail): self
{
    $this->toEmail = $toEmail;

    return $this;
}


  
    // TCMSFieldVarchar
public function getToName(): string
{
    return $this->toName;
}
public function setToName(string $toName): self
{
    $this->toName = $toName;

    return $this;
}


  
}
