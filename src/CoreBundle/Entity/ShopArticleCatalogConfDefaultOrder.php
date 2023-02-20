<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleCatalogConfDefaultOrder {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleCatalogConf|null - Belongs to configuration */
private \ChameleonSystem\CoreBundle\Entity\ShopArticleCatalogConf|null $shopArticleCatalogConf = null,
/** @var null|string - Belongs to configuration */
private ?string $shopArticleCatalogConfId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby|null - Sorting */
private \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby|null $shopModuleArticlelistOrderby = null,
/** @var null|string - Sorting */
private ?string $shopModuleArticlelistOrderbyId = null
, 
    // TCMSFieldVarchar
/** @var string - Name (description) */
private string $name = '', 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory[] - Category */
private \Doctrine\Common\Collections\Collection $shopCategoryMlt = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getShopArticleCatalogConf(): \ChameleonSystem\CoreBundle\Entity\ShopArticleCatalogConf|null
{
    return $this->shopArticleCatalogConf;
}
public function setShopArticleCatalogConf(\ChameleonSystem\CoreBundle\Entity\ShopArticleCatalogConf|null $shopArticleCatalogConf): self
{
    $this->shopArticleCatalogConf = $shopArticleCatalogConf;
    $this->shopArticleCatalogConfId = $shopArticleCatalogConf?->getId();

    return $this;
}
public function getShopArticleCatalogConfId(): ?string
{
    return $this->shopArticleCatalogConfId;
}
public function setShopArticleCatalogConfId(?string $shopArticleCatalogConfId): self
{
    $this->shopArticleCatalogConfId = $shopArticleCatalogConfId;
    // todo - load new id
    //$this->shopArticleCatalogConfId = $?->getId();

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


  
    // TCMSFieldLookup
public function getShopModuleArticlelistOrderby(): \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby|null
{
    return $this->shopModuleArticlelistOrderby;
}
public function setShopModuleArticlelistOrderby(\ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby|null $shopModuleArticlelistOrderby): self
{
    $this->shopModuleArticlelistOrderby = $shopModuleArticlelistOrderby;
    $this->shopModuleArticlelistOrderbyId = $shopModuleArticlelistOrderby?->getId();

    return $this;
}
public function getShopModuleArticlelistOrderbyId(): ?string
{
    return $this->shopModuleArticlelistOrderbyId;
}
public function setShopModuleArticlelistOrderbyId(?string $shopModuleArticlelistOrderbyId): self
{
    $this->shopModuleArticlelistOrderbyId = $shopModuleArticlelistOrderbyId;
    // todo - load new id
    //$this->shopModuleArticlelistOrderbyId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookupMultiselect
public function getShopCategoryMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopCategoryMlt;
}
public function setShopCategoryMlt(\Doctrine\Common\Collections\Collection $shopCategoryMlt): self
{
    $this->shopCategoryMlt = $shopCategoryMlt;

    return $this;
}


  
}
