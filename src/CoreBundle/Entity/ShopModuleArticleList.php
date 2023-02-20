<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance;
use ChameleonSystem\CoreBundle\Entity\ShopModuleArticleListArticle;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class ShopModuleArticleList {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var CmsTplModuleInstance|null - Belongs to module instance */
private ?CmsTplModuleInstance $cmsTplModuleInstance = null
, 
    // TCMSFieldVarchar
/** @var string - Headline */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Number of articles shown */
private string $numberOfArticles = '-1', 
    // TCMSFieldVarchar
/** @var string - Number of articles per page */
private string $numberOfArticlesPerPage = '10', 
    // TCMSFieldPropertyTable
/** @var Collection<int, shopModuleArticleListArticle> - Show these articles */
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
    // TCMSFieldLookupParentID
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
* @return Collection<int, shopModuleArticleListArticle>
*/
public function getShopModuleArticleListArticleCollection(): Collection
{
    return $this->shopModuleArticleListArticleCollection;
}

public function addShopModuleArticleListArticleCollection(shopModuleArticleListArticle $shopModuleArticleListArticle): self
{
    if (!$this->shopModuleArticleListArticleCollection->contains($shopModuleArticleListArticle)) {
        $this->shopModuleArticleListArticleCollection->add($shopModuleArticleListArticle);
        $shopModuleArticleListArticle->setShopModuleArticleList($this);
    }

    return $this;
}

public function removeShopModuleArticleListArticleCollection(shopModuleArticleListArticle $shopModuleArticleListArticle): self
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
