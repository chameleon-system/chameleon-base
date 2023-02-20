<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopShippingType {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldPrice
/** @var float - Additional costs */
private float $value = 0, 
    // TCMSFieldOption
/** @var string - Addtional costs type */
private string $valueType = 'absolut', 
    // TCMSFieldBoolean
/** @var bool - Value relates to the whole basket */
private bool $valueBasedOnEntireBasket = false, 
    // TCMSFieldPrice
/** @var float - Additional charges */
private float $valueAdditional = 0, 
    // TCMSFieldPrice
/** @var float - Maximum additional charges */
private float $valueMax = 0, 
    // TCMSFieldPrice
/** @var float - Minimum additional charges */
private float $valueMin = 0, 
    // TCMSFieldBoolean
/** @var bool - Calculate shipping costs for each item separately */
private bool $addValueForEachArticle = false, 
    // TCMSFieldBoolean
/** @var bool - Use for logged in users only */
private bool $restrictToSignedInUsers = false, 
    // TCMSFieldBoolean
/** @var bool - Apply to all products with at least one match */
private bool $applyToAllProducts = false, 
    // TCMSFieldBoolean
/** @var bool - When applied, ignore all other shipping costs types */
private bool $endShippingTypeChain = false, 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0, 
    // TCMSFieldBoolean
/** @var bool - Active */
private bool $active = false, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Active as of */
private \DateTime|null $activeFrom = null, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Active until */
private \DateTime|null $activeTo = null, 
    // TCMSFieldPrice
/** @var float - Minimum value of affected items (Euro) */
private float $restrictToValueFrom = 0, 
    // TCMSFieldPrice
/** @var float - Maximum value of affected items (Euro) */
private float $restrictToValueTo = 0, 
    // TCMSFieldNumber
/** @var int - Minimum amount of items affected */
private int $restrictToArticlesFrom = 0, 
    // TCMSFieldNumber
/** @var int - Maximum amount of items affected */
private int $restrictToArticlesTo = 0, 
    // TCMSFieldNumber
/** @var int - Minimum weight of affected items (grams) */
private int $restrictToWeightFrom = 0, 
    // TCMSFieldNumber
/** @var int - Maximum weight of affected items (grams) */
private int $restrictToWeightTo = 0, 
    // TCMSFieldDecimal
/** @var float - Minimum volume of affected items (cubic meters) */
private float $restrictToVolumeFrom = 0, 
    // TCMSFieldDecimal
/** @var float - Maximum volume of affected items (cubic meters) */
private float $restrictToVolumeTo = 0, 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleGroup[] - Restrict to following product groups */
private \Doctrine\Common\Collections\Collection $shopArticleGroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory[] - Restrict to following product categories */
private \Doctrine\Common\Collections\Collection $shopCategoryMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle[] - Restrict to following items */
private \Doctrine\Common\Collections\Collection $shopArticleMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\DataCountry[] - Restrict to following shipping countries */
private \Doctrine\Common\Collections\Collection $dataCountryMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser[] - Restrict to following users */
private \Doctrine\Common\Collections\Collection $dataExtranetUserMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] - Restrict to following customer groups */
private \Doctrine\Common\Collections\Collection $dataExtranetGroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] - Restrict to following portals */
private \Doctrine\Common\Collections\Collection $cmsPortalMlt = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function isValueBasedOnEntireBasket(): bool
{
    return $this->valueBasedOnEntireBasket;
}
public function setValueBasedOnEntireBasket(bool $valueBasedOnEntireBasket): self
{
    $this->valueBasedOnEntireBasket = $valueBasedOnEntireBasket;

    return $this;
}


  
    // TCMSFieldPrice
public function getValueAdditional(): float
{
    return $this->valueAdditional;
}
public function setValueAdditional(float $valueAdditional): self
{
    $this->valueAdditional = $valueAdditional;

    return $this;
}


  
    // TCMSFieldPrice
public function getValueMax(): float
{
    return $this->valueMax;
}
public function setValueMax(float $valueMax): self
{
    $this->valueMax = $valueMax;

    return $this;
}


  
    // TCMSFieldPrice
public function getValueMin(): float
{
    return $this->valueMin;
}
public function setValueMin(float $valueMin): self
{
    $this->valueMin = $valueMin;

    return $this;
}


  
    // TCMSFieldBoolean
public function isAddValueForEachArticle(): bool
{
    return $this->addValueForEachArticle;
}
public function setAddValueForEachArticle(bool $addValueForEachArticle): self
{
    $this->addValueForEachArticle = $addValueForEachArticle;

    return $this;
}


  
    // TCMSFieldBoolean
public function isRestrictToSignedInUsers(): bool
{
    return $this->restrictToSignedInUsers;
}
public function setRestrictToSignedInUsers(bool $restrictToSignedInUsers): self
{
    $this->restrictToSignedInUsers = $restrictToSignedInUsers;

    return $this;
}


  
    // TCMSFieldBoolean
public function isApplyToAllProducts(): bool
{
    return $this->applyToAllProducts;
}
public function setApplyToAllProducts(bool $applyToAllProducts): self
{
    $this->applyToAllProducts = $applyToAllProducts;

    return $this;
}


  
    // TCMSFieldBoolean
public function isEndShippingTypeChain(): bool
{
    return $this->endShippingTypeChain;
}
public function setEndShippingTypeChain(bool $endShippingTypeChain): self
{
    $this->endShippingTypeChain = $endShippingTypeChain;

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


  
    // TCMSFieldNumber
public function getRestrictToWeightFrom(): int
{
    return $this->restrictToWeightFrom;
}
public function setRestrictToWeightFrom(int $restrictToWeightFrom): self
{
    $this->restrictToWeightFrom = $restrictToWeightFrom;

    return $this;
}


  
    // TCMSFieldNumber
public function getRestrictToWeightTo(): int
{
    return $this->restrictToWeightTo;
}
public function setRestrictToWeightTo(int $restrictToWeightTo): self
{
    $this->restrictToWeightTo = $restrictToWeightTo;

    return $this;
}


  
    // TCMSFieldDecimal
public function getRestrictToVolumeFrom(): float
{
    return $this->restrictToVolumeFrom;
}
public function setRestrictToVolumeFrom(float $restrictToVolumeFrom): self
{
    $this->restrictToVolumeFrom = $restrictToVolumeFrom;

    return $this;
}


  
    // TCMSFieldDecimal
public function getRestrictToVolumeTo(): float
{
    return $this->restrictToVolumeTo;
}
public function setRestrictToVolumeTo(float $restrictToVolumeTo): self
{
    $this->restrictToVolumeTo = $restrictToVolumeTo;

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


  
    // TCMSFieldLookupMultiselect
public function getShopArticleMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleMlt;
}
public function setShopArticleMlt(\Doctrine\Common\Collections\Collection $shopArticleMlt): self
{
    $this->shopArticleMlt = $shopArticleMlt;

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


  
    // TCMSFieldLookupMultiselect
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
public function getDataExtranetGroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->dataExtranetGroupMlt;
}
public function setDataExtranetGroupMlt(\Doctrine\Common\Collections\Collection $dataExtranetGroupMlt): self
{
    $this->dataExtranetGroupMlt = $dataExtranetGroupMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getCmsPortalMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsPortalMlt;
}
public function setCmsPortalMlt(\Doctrine\Common\Collections\Collection $cmsPortalMlt): self
{
    $this->cmsPortalMlt = $cmsPortalMlt;

    return $this;
}


  
}
