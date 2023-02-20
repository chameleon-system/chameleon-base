<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleCatalogConf {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null - Belongs to module instance */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance = null,
/** @var null|string - Belongs to module instance */
private ?string $cmsTplModuleInstanceId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby|null - Default sorting */
private \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby|null $shopModuleArticlelistOrderby = null,
/** @var null|string - Default sorting */
private ?string $shopModuleArticlelistOrderbyId = null
, 
    // TCMSFieldVarchar
/** @var string - Title / headline */
private string $name = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleCatalogConfDefaultOrder[] - Alternative default sorting */
private \Doctrine\Common\Collections\Collection $shopArticleCatalogConfDefaultOrderCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldBoolean
/** @var bool - Offer Reserving at 0 stock */
private bool $showSubcategoryProducts = false, 
    // TCMSFieldNumber
/** @var int - Articles per page */
private int $pageSize = 20, 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby[] - Available sortings */
private \Doctrine\Common\Collections\Collection $shopModuleArticlelistOrderbyMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldWYSIWYG
/** @var string - Introduction text */
private string $intro = ''  ) {}

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
public function getCmsTplModuleInstance(): \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null
{
    return $this->cmsTplModuleInstance;
}
public function setCmsTplModuleInstance(\ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance): self
{
    $this->cmsTplModuleInstance = $cmsTplModuleInstance;
    $this->cmsTplModuleInstanceId = $cmsTplModuleInstance?->getId();

    return $this;
}
public function getCmsTplModuleInstanceId(): ?string
{
    return $this->cmsTplModuleInstanceId;
}
public function setCmsTplModuleInstanceId(?string $cmsTplModuleInstanceId): self
{
    $this->cmsTplModuleInstanceId = $cmsTplModuleInstanceId;
    // todo - load new id
    //$this->cmsTplModuleInstanceId = $?->getId();

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


  
    // TCMSFieldPropertyTable
public function getShopArticleCatalogConfDefaultOrderCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleCatalogConfDefaultOrderCollection;
}
public function setShopArticleCatalogConfDefaultOrderCollection(\Doctrine\Common\Collections\Collection $shopArticleCatalogConfDefaultOrderCollection): self
{
    $this->shopArticleCatalogConfDefaultOrderCollection = $shopArticleCatalogConfDefaultOrderCollection;

    return $this;
}


  
    // TCMSFieldBoolean
public function isShowSubcategoryProducts(): bool
{
    return $this->showSubcategoryProducts;
}
public function setShowSubcategoryProducts(bool $showSubcategoryProducts): self
{
    $this->showSubcategoryProducts = $showSubcategoryProducts;

    return $this;
}


  
    // TCMSFieldNumber
public function getPageSize(): int
{
    return $this->pageSize;
}
public function setPageSize(int $pageSize): self
{
    $this->pageSize = $pageSize;

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



  
    // TCMSFieldLookupMultiselectCheckboxes
public function getShopModuleArticlelistOrderbyMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopModuleArticlelistOrderbyMlt;
}
public function setShopModuleArticlelistOrderbyMlt(\Doctrine\Common\Collections\Collection $shopModuleArticlelistOrderbyMlt): self
{
    $this->shopModuleArticlelistOrderbyMlt = $shopModuleArticlelistOrderbyMlt;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getIntro(): string
{
    return $this->intro;
}
public function setIntro(string $intro): self
{
    $this->intro = $intro;

    return $this;
}


  
}
