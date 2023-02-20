<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopModuleArticleList;

class ShopModuleArticleListArticle {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var ShopModuleArticleList|null - Belongs to article list */
private ?ShopModuleArticleList $shopModuleArticleList = null
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
    // TCMSFieldLookupParentID
public function getShopModuleArticleList(): ?ShopModuleArticleList
{
    return $this->shopModuleArticleList;
}

public function setShopModuleArticleList(?ShopModuleArticleList $shopModuleArticleList): self
{
    $this->shopModuleArticleList = $shopModuleArticleList;

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
