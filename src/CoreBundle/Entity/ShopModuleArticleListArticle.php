<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopModuleArticleList;
use ChameleonSystem\CoreBundle\Entity\ShopArticle;

class ShopModuleArticleListArticle {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var ShopModuleArticleList|null - Belongs to article list */
private ?ShopModuleArticleList $shopModuleArticleList = null
, 
    // TCMSFieldLookup
/** @var ShopArticle|null - Article */
private ?ShopArticle $shopArticle = null
, 
    // TCMSFieldVarchar
/** @var string - Alternative headline */
private string $name = ''  ) {}

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
public function getShopModuleArticleList(): ?ShopModuleArticleList
{
    return $this->shopModuleArticleList;
}

public function setShopModuleArticleList(?ShopModuleArticleList $shopModuleArticleList): self
{
    $this->shopModuleArticleList = $shopModuleArticleList;

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
public function getName(): string
{
    return $this->name;
}
public function setName(string $name): self
{
    $this->name = $name;

    return $this;
}


  
}
