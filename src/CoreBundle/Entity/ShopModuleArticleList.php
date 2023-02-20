<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopModuleArticleList {
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
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Icon */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $icon = null,
/** @var null|string - Icon */
private ?string $iconId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopModuleArticleListFilter|null - Filter / content */
private \ChameleonSystem\CoreBundle\Entity\ShopModuleArticleListFilter|null $shopModuleArticleListFilter = null,
/** @var null|string - Filter / content */
private ?string $shopModuleArticleListFilterId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby|null - Sorting */
private \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby|null $shopModuleArticlelistOrderby = null,
/** @var null|string - Sorting */
private ?string $shopModuleArticlelistOrderbyId = null
, 
    // TCMSFieldBoolean
/** @var bool - Release for the Post-Search-Filter */
private bool $canBeFiltered = false, 
    // TCMSFieldVarchar
/** @var string - Headline */
private string $name = '', 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby[] - Available sortings */
private \Doctrine\Common\Collections\Collection $shopModuleArticlelistOrderbyMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldNumber
/** @var int - Number of articles shown */
private int $numberOfArticles = -1, 
    // TCMSFieldNumber
/** @var int - Number of articles per page */
private int $numberOfArticlesPerPage = 10, 
    // TCMSFieldWYSIWYG
/** @var string - Introduction text */
private string $descriptionStart = '', 
    // TCMSFieldWYSIWYG
/** @var string - Closing text */
private string $descriptionEnd = '', 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleGroup[] - Show articles from these article groups */
private \Doctrine\Common\Collections\Collection $shopArticleGroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory[] - Show articles from these product categories */
private \Doctrine\Common\Collections\Collection $shopCategoryMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopModuleArticleListArticle[] - Show these articles */
private \Doctrine\Common\Collections\Collection $shopModuleArticleListArticleCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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



  
    // TCMSFieldBoolean
public function isCanBeFiltered(): bool
{
    return $this->canBeFiltered;
}
public function setCanBeFiltered(bool $canBeFiltered): self
{
    $this->canBeFiltered = $canBeFiltered;

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
public function getIcon(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->icon;
}
public function setIcon(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $icon): self
{
    $this->icon = $icon;
    $this->iconId = $icon?->getId();

    return $this;
}
public function getIconId(): ?string
{
    return $this->iconId;
}
public function setIconId(?string $iconId): self
{
    $this->iconId = $iconId;
    // todo - load new id
    //$this->iconId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getShopModuleArticleListFilter(): \ChameleonSystem\CoreBundle\Entity\ShopModuleArticleListFilter|null
{
    return $this->shopModuleArticleListFilter;
}
public function setShopModuleArticleListFilter(\ChameleonSystem\CoreBundle\Entity\ShopModuleArticleListFilter|null $shopModuleArticleListFilter): self
{
    $this->shopModuleArticleListFilter = $shopModuleArticleListFilter;
    $this->shopModuleArticleListFilterId = $shopModuleArticleListFilter?->getId();

    return $this;
}
public function getShopModuleArticleListFilterId(): ?string
{
    return $this->shopModuleArticleListFilterId;
}
public function setShopModuleArticleListFilterId(?string $shopModuleArticleListFilterId): self
{
    $this->shopModuleArticleListFilterId = $shopModuleArticleListFilterId;
    // todo - load new id
    //$this->shopModuleArticleListFilterId = $?->getId();

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


  
    // TCMSFieldNumber
public function getNumberOfArticles(): int
{
    return $this->numberOfArticles;
}
public function setNumberOfArticles(int $numberOfArticles): self
{
    $this->numberOfArticles = $numberOfArticles;

    return $this;
}


  
    // TCMSFieldNumber
public function getNumberOfArticlesPerPage(): int
{
    return $this->numberOfArticlesPerPage;
}
public function setNumberOfArticlesPerPage(int $numberOfArticlesPerPage): self
{
    $this->numberOfArticlesPerPage = $numberOfArticlesPerPage;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getDescriptionStart(): string
{
    return $this->descriptionStart;
}
public function setDescriptionStart(string $descriptionStart): self
{
    $this->descriptionStart = $descriptionStart;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getDescriptionEnd(): string
{
    return $this->descriptionEnd;
}
public function setDescriptionEnd(string $descriptionEnd): self
{
    $this->descriptionEnd = $descriptionEnd;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getShopArticleGroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleGroupMlt;
}
public function setShopArticleGroupMlt(\Doctrine\Common\Collections\Collection $shopArticleGroupMlt): self
{
    $this->shopArticleGroupMlt = $shopArticleGroupMlt;

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


  
    // TCMSFieldPropertyTable
public function getShopModuleArticleListArticleCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopModuleArticleListArticleCollection;
}
public function setShopModuleArticleListArticleCollection(\Doctrine\Common\Collections\Collection $shopModuleArticleListArticleCollection): self
{
    $this->shopModuleArticleListArticleCollection = $shopModuleArticleListArticleCollection;

    return $this;
}


  
}
