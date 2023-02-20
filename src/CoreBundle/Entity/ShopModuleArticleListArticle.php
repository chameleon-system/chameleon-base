<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopModuleArticleListArticle {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopModuleArticleList|null - Belongs to article list */
private \ChameleonSystem\CoreBundle\Entity\ShopModuleArticleList|null $shopModuleArticleList = null,
/** @var null|string - Belongs to article list */
private ?string $shopModuleArticleListId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle|null - Article */
private \ChameleonSystem\CoreBundle\Entity\ShopArticle|null $shopArticle = null,
/** @var null|string - Article */
private ?string $shopArticleId = null
, 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0, 
    // TCMSFieldVarchar
/** @var string - Alternative headline */
private string $name = ''  ) {}

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
public function getShopModuleArticleList(): \ChameleonSystem\CoreBundle\Entity\ShopModuleArticleList|null
{
    return $this->shopModuleArticleList;
}
public function setShopModuleArticleList(\ChameleonSystem\CoreBundle\Entity\ShopModuleArticleList|null $shopModuleArticleList): self
{
    $this->shopModuleArticleList = $shopModuleArticleList;
    $this->shopModuleArticleListId = $shopModuleArticleList?->getId();

    return $this;
}
public function getShopModuleArticleListId(): ?string
{
    return $this->shopModuleArticleListId;
}
public function setShopModuleArticleListId(?string $shopModuleArticleListId): self
{
    $this->shopModuleArticleListId = $shopModuleArticleListId;
    // todo - load new id
    //$this->shopModuleArticleListId = $?->getId();

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
