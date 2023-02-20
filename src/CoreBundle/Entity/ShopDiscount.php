<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopDiscount {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldPrice
/** @var float - Value */
private float $value = 0, 
    // TCMSFieldOption
/** @var string - Value type */
private string $valueType = 'absolut', 
    // TCMSFieldBoolean
/** @var bool - Show percentual discount on detailed product page */
private bool $showDiscountOnArticleDetailpage = false, 
    // TCMSFieldBoolean
/** @var bool - Active */
private bool $active = false, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Valid from */
private \DateTime|null $activeFrom = null, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Active until */
private \DateTime|null $activeTo = null, 
    // TCMSFieldPosition
/** @var int - Sorting */
private int $position = 0, 
    // TCMSFieldNumber
/** @var int - Min. amount of products affected */
private int $restrictToArticlesFrom = 0, 
    // TCMSFieldNumber
/** @var int - Max. amount of products affected */
private int $restrictToArticlesTo = 0, 
    // TCMSFieldPrice
/** @var float - Minimum value of affected products (Euro) */
private float $restrictToValueFrom = 0, 
    // TCMSFieldPrice
/** @var float - Maximum value of affected products (Euro) */
private float $restrictToValueTo = 0, 
    // TCMSFieldLookupMultiSelectRestriction
/** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory[] - Restrict to following product categories */
private \Doctrine\Common\Collections\Collection $shopCategoryMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiSelectRestriction
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle[] - Restrict to following products */
private \Doctrine\Common\Collections\Collection $shopArticleMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiSelectRestriction
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] - Restrict to following customer groups */
private \Doctrine\Common\Collections\Collection $dataExtranetGroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiSelectRestriction
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser[] - Restrict to following customers */
private \Doctrine\Common\Collections\Collection $dataExtranetUserMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\DataCountry[] - Restrict to following shipping countries */
private \Doctrine\Common\Collections\Collection $dataCountryMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldWYSIWYG
/** @var string - Description */
private string $description = '', 
    // TCMSFieldDateTime
/** @var \DateTime|null - When has the cache of the affected products been cleared the last time? */
private \DateTime|null $cacheClearLastExecuted = null  ) {}

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


  
    // TCMSFieldPrice
public function getValue(): float
{
    return $this->value;
}
public function setValue(float $value): self
{
    $this->value = $value;

    return $this;
}


  
    // TCMSFieldOption
public function getValueType(): string
{
    return $this->valueType;
}
public function setValueType(string $valueType): self
{
    $this->valueType = $valueType;

    return $this;
}


  
    // TCMSFieldBoolean
public function isShowDiscountOnArticleDetailpage(): bool
{
    return $this->showDiscountOnArticleDetailpage;
}
public function setShowDiscountOnArticleDetailpage(bool $showDiscountOnArticleDetailpage): self
{
    $this->showDiscountOnArticleDetailpage = $showDiscountOnArticleDetailpage;

    return $this;
}


  
    // TCMSFieldBoolean
public function isActive(): bool
{
    return $this->active;
}
public function setActive(bool $active): self
{
    $this->active = $active;

    return $this;
}


  
    // TCMSFieldDateTime
public function getActiveFrom(): \DateTime|null
{
    return $this->activeFrom;
}
public function setActiveFrom(\DateTime|null $activeFrom): self
{
    $this->activeFrom = $activeFrom;

    return $this;
}


  
    // TCMSFieldDateTime
public function getActiveTo(): \DateTime|null
{
    return $this->activeTo;
}
public function setActiveTo(\DateTime|null $activeTo): self
{
    $this->activeTo = $activeTo;

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


  
    // TCMSFieldNumber
public function getRestrictToArticlesFrom(): int
{
    return $this->restrictToArticlesFrom;
}
public function setRestrictToArticlesFrom(int $restrictToArticlesFrom): self
{
    $this->restrictToArticlesFrom = $restrictToArticlesFrom;

    return $this;
}


  
    // TCMSFieldNumber
public function getRestrictToArticlesTo(): int
{
    return $this->restrictToArticlesTo;
}
public function setRestrictToArticlesTo(int $restrictToArticlesTo): self
{
    $this->restrictToArticlesTo = $restrictToArticlesTo;

    return $this;
}


  
    // TCMSFieldPrice
public function getRestrictToValueFrom(): float
{
    return $this->restrictToValueFrom;
}
public function setRestrictToValueFrom(float $restrictToValueFrom): self
{
    $this->restrictToValueFrom = $restrictToValueFrom;

    return $this;
}


  
    // TCMSFieldPrice
public function getRestrictToValueTo(): float
{
    return $this->restrictToValueTo;
}
public function setRestrictToValueTo(float $restrictToValueTo): self
{
    $this->restrictToValueTo = $restrictToValueTo;

    return $this;
}


  
    // TCMSFieldLookupMultiSelectRestriction
public function getShopCategoryMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopCategoryMlt;
}
public function setShopCategoryMlt(\Doctrine\Common\Collections\Collection $shopCategoryMlt): self
{
    $this->shopCategoryMlt = $shopCategoryMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiSelectRestriction
public function getShopArticleMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleMlt;
}
public function setShopArticleMlt(\Doctrine\Common\Collections\Collection $shopArticleMlt): self
{
    $this->shopArticleMlt = $shopArticleMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiSelectRestriction
public function getDataExtranetGroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->dataExtranetGroupMlt;
}
public function setDataExtranetGroupMlt(\Doctrine\Common\Collections\Collection $dataExtranetGroupMlt): self
{
    $this->dataExtranetGroupMlt = $dataExtranetGroupMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiSelectRestriction
public function getDataExtranetUserMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->dataExtranetUserMlt;
}
public function setDataExtranetUserMlt(\Doctrine\Common\Collections\Collection $dataExtranetUserMlt): self
{
    $this->dataExtranetUserMlt = $dataExtranetUserMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getDataCountryMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->dataCountryMlt;
}
public function setDataCountryMlt(\Doctrine\Common\Collections\Collection $dataCountryMlt): self
{
    $this->dataCountryMlt = $dataCountryMlt;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getDescription(): string
{
    return $this->description;
}
public function setDescription(string $description): self
{
    $this->description = $description;

    return $this;
}


  
    // TCMSFieldDateTime
public function getCacheClearLastExecuted(): \DateTime|null
{
    return $this->cacheClearLastExecuted;
}
public function setCacheClearLastExecuted(\DateTime|null $cacheClearLastExecuted): self
{
    $this->cacheClearLastExecuted = $cacheClearLastExecuted;

    return $this;
}


  
}
