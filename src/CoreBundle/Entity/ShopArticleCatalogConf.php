<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance;
use ChameleonSystem\CoreBundle\Entity\ShopArticleCatalogConfDefaultOrder;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby;

class ShopArticleCatalogConf {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsTplModuleInstance|null - Belongs to module instance */
private ?CmsTplModuleInstance $cmsTplModuleInstance = null
, 
    // TCMSFieldVarchar
/** @var string - Title / headline */
private string $name = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopArticleCatalogConfDefaultOrder> - Alternative default sorting */
private Collection $shopArticleCatalogConfDefaultOrderCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Articles per page */
private string $pageSize = '20', 
    // TCMSFieldLookup
/** @var ShopModuleArticlelistOrderby|null - Default sorting */
private ?ShopModuleArticlelistOrderby $shopModuleArticlelistOrderby = null
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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopArticleCatalogConfDefaultOrder>
*/
public function getShopArticleCatalogConfDefaultOrderCollection(): Collection
{
    return $this->shopArticleCatalogConfDefaultOrderCollection;
}

public function addShopArticleCatalogConfDefaultOrderCollection(ShopArticleCatalogConfDefaultOrder $shopArticleCatalogConfDefaultOrder): self
{
    if (!$this->shopArticleCatalogConfDefaultOrderCollection->contains($shopArticleCatalogConfDefaultOrder)) {
        $this->shopArticleCatalogConfDefaultOrderCollection->add($shopArticleCatalogConfDefaultOrder);
        $shopArticleCatalogConfDefaultOrder->setShopArticleCatalogConf($this);
    }

    return $this;
}

public function removeShopArticleCatalogConfDefaultOrderCollection(ShopArticleCatalogConfDefaultOrder $shopArticleCatalogConfDefaultOrder): self
{
    if ($this->shopArticleCatalogConfDefaultOrderCollection->removeElement($shopArticleCatalogConfDefaultOrder)) {
        // set the owning side to null (unless already changed)
        if ($shopArticleCatalogConfDefaultOrder->getShopArticleCatalogConf() === $this) {
            $shopArticleCatalogConfDefaultOrder->setShopArticleCatalogConf(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getPageSize(): string
{
    return $this->pageSize;
}
public function setPageSize(string $pageSize): self
{
    $this->pageSize = $pageSize;

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


  
}
