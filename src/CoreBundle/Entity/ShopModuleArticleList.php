<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance;
use ChameleonSystem\CoreBundle\Entity\CmsMedia;
use ChameleonSystem\CoreBundle\Entity\ShopModuleArticleListFilter;
use ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby;
use ChameleonSystem\CoreBundle\Entity\ShopModuleArticleListArticle;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class ShopModuleArticleList {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsTplModuleInstance|null - Belongs to module instance */
private ?CmsTplModuleInstance $cmsTplModuleInstance = null
, 
    // TCMSFieldVarchar
/** @var string - Headline */
private string $name = '', 
    // TCMSFieldLookup
/** @var CmsMedia|null - Icon */
private ?CmsMedia $i = null
, 
    // TCMSFieldLookup
/** @var ShopModuleArticleListFilter|null - Filter / content */
private ?ShopModuleArticleListFilter $shopModuleArticleListFilter = null
, 
    // TCMSFieldLookup
/** @var ShopModuleArticlelistOrderby|null - Sorting */
private ?ShopModuleArticlelistOrderby $shopModuleArticlelistOrderby = null
, 
    // TCMSFieldVarchar
/** @var string - Number of articles shown */
private string $numberOfArticles = '-1', 
    // TCMSFieldVarchar
/** @var string - Number of articles per page */
private string $numberOfArticlesPerPage = '10', 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopModuleArticleListArticle> - Show these articles */
private Collection $shopModuleArticleListArticleCollection = new ArrayCollection()
  ) {}

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
public function getCmsTplModuleInstance(): ?CmsTplModuleInstance
{
    return $this->cmsTplModuleInstance;
}

public function setCmsTplModuleInstance(?CmsTplModuleInstance $cmsTplModuleInstance): self
{
    $this->cmsTplModuleInstance = $cmsTplModuleInstance;

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
public function getI(): ?CmsMedia
{
    return $this->i;
}

public function setI(?CmsMedia $i): self
{
    $this->i = $i;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopModuleArticleListFilter(): ?ShopModuleArticleListFilter
{
    return $this->shopModuleArticleListFilter;
}

public function setShopModuleArticleListFilter(?ShopModuleArticleListFilter $shopModuleArticleListFilter): self
{
    $this->shopModuleArticleListFilter = $shopModuleArticleListFilter;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopModuleArticlelistOrderby(): ?ShopModuleArticlelistOrderby
{
    return $this->shopModuleArticlelistOrderby;
}

public function setShopModuleArticlelistOrderby(?ShopModuleArticlelistOrderby $shopModuleArticlelistOrderby): self
{
    $this->shopModuleArticlelistOrderby = $shopModuleArticlelistOrderby;

    return $this;
}


  
    // TCMSFieldVarchar
public function getNumberOfArticles(): string
{
    return $this->numberOfArticles;
}
public function setNumberOfArticles(string $numberOfArticles): self
{
    $this->numberOfArticles = $numberOfArticles;

    return $this;
}


  
    // TCMSFieldVarchar
public function getNumberOfArticlesPerPage(): string
{
    return $this->numberOfArticlesPerPage;
}
public function setNumberOfArticlesPerPage(string $numberOfArticlesPerPage): self
{
    $this->numberOfArticlesPerPage = $numberOfArticlesPerPage;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopModuleArticleListArticle>
*/
public function getShopModuleArticleListArticleCollection(): Collection
{
    return $this->shopModuleArticleListArticleCollection;
}

public function addShopModuleArticleListArticleCollection(ShopModuleArticleListArticle $shopModuleArticleListArticle): self
{
    if (!$this->shopModuleArticleListArticleCollection->contains($shopModuleArticleListArticle)) {
        $this->shopModuleArticleListArticleCollection->add($shopModuleArticleListArticle);
        $shopModuleArticleListArticle->setShopModuleArticleList($this);
    }

    return $this;
}

public function removeShopModuleArticleListArticleCollection(ShopModuleArticleListArticle $shopModuleArticleListArticle): self
{
    if ($this->shopModuleArticleListArticleCollection->removeElement($shopModuleArticleListArticle)) {
        // set the owning side to null (unless already changed)
        if ($shopModuleArticleListArticle->getShopModuleArticleList() === $this) {
            $shopModuleArticleListArticle->setShopModuleArticleList(null);
        }
    }

    return $this;
}


  
}
