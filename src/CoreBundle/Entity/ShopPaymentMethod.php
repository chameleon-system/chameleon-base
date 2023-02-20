<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopPaymentMethod {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null - Belongs to payment provider */
private \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null $shopPaymentHandlerGroup = null,
/** @var null|string - Belongs to payment provider */
private ?string $shopPaymentHandlerGroupId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandler|null - Payment handler */
private \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandler|null $shopPaymentHandler = null,
/** @var null|string - Payment handler */
private ?string $shopPaymentHandlerId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVat|null - VAT group */
private \ChameleonSystem\CoreBundle\Entity\ShopVat|null $shopVat = null,
/** @var null|string - VAT group */
private ?string $shopVatId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Icon */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia = null,
/** @var null|string - Icon */
private ?string $cmsMediaId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Internal system name */
private string $nameInternal = '', 
    // TCMSFieldBoolean
/** @var bool - Active */
private bool $active = false, 
    // TCMSFieldBoolean
/** @var bool - Allow for Packstation delivery addresses */
private bool $pkgDhlPackstationAllowForPackstation = true, 
    // TCMSFieldPosition
/** @var int - Sorting */
private int $position = 0, 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] - Restrict to the following portals */
private \Doctrine\Common\Collections\Collection $cmsPortalMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPrice
/** @var float - Available from merchandise value */
private float $restrictToValueFrom = 0, 
    // TCMSFieldPrice
/** @var float - Available until merchandise value */
private float $restrictToValueTo = 0, 
    // TCMSFieldDecimal
/** @var float - Available from basket value */
private float $restrictToBasketValueFrom = 0, 
    // TCMSFieldDecimal
/** @var float - Available to basket value */
private float $restrictToBasketValueTo = 0, 
    // TCMSFieldPrice
/** @var float - Additional costs */
private float $value = 0, 
    // TCMSFieldOption
/** @var string - Additional costs type */
private string $valueType = 'absolut', 
    // TCMSFieldWYSIWYG
/** @var string - Description */
private string $description = '', 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser[] - Restrict to following customers */
private \Doctrine\Common\Collections\Collection $dataExtranetUserMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] - Restrict to following customer groups */
private \Doctrine\Common\Collections\Collection $dataExtranetGroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\DataCountry[] - Restrict to following shipping countries */
private \Doctrine\Common\Collections\Collection $dataCountryMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\DataCountry[] - Restrict to following billing countries */
private \Doctrine\Common\Collections\Collection $dataCountryBilling = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldBoolean
/** @var bool - Use not fixed positive list match */
private bool $positivListLooseMatch = false, 
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
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleGroup[] - Do not allow for following product groups */
private \Doctrine\Common\Collections\Collection $shopArticleGroup1Mlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory[] - Do not allow for following product categories */
private \Doctrine\Common\Collections\Collection $shopCategory1Mlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle[] - Do not allow for following products */
private \Doctrine\Common\Collections\Collection $shopArticle1Mlt = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getShopPaymentHandlerGroup(): \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null
{
    return $this->shopPaymentHandlerGroup;
}
public function setShopPaymentHandlerGroup(\ChameleonSystem\CoreBundle\Entity\ShopPaymentHandlerGroup|null $shopPaymentHandlerGroup): self
{
    $this->shopPaymentHandlerGroup = $shopPaymentHandlerGroup;
    $this->shopPaymentHandlerGroupId = $shopPaymentHandlerGroup?->getId();

    return $this;
}
public function getShopPaymentHandlerGroupId(): ?string
{
    return $this->shopPaymentHandlerGroupId;
}
public function setShopPaymentHandlerGroupId(?string $shopPaymentHandlerGroupId): self
{
    $this->shopPaymentHandlerGroupId = $shopPaymentHandlerGroupId;
    // todo - load new id
    //$this->shopPaymentHandlerGroupId = $?->getId();

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
public function getNameInternal(): string
{
    return $this->nameInternal;
}
public function setNameInternal(string $nameInternal): self
{
    $this->nameInternal = $nameInternal;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopPaymentHandler(): \ChameleonSystem\CoreBundle\Entity\ShopPaymentHandler|null
{
    return $this->shopPaymentHandler;
}
public function setShopPaymentHandler(\ChameleonSystem\CoreBundle\Entity\ShopPaymentHandler|null $shopPaymentHandler): self
{
    $this->shopPaymentHandler = $shopPaymentHandler;
    $this->shopPaymentHandlerId = $shopPaymentHandler?->getId();

    return $this;
}
public function getShopPaymentHandlerId(): ?string
{
    return $this->shopPaymentHandlerId;
}
public function setShopPaymentHandlerId(?string $shopPaymentHandlerId): self
{
    $this->shopPaymentHandlerId = $shopPaymentHandlerId;
    // todo - load new id
    //$this->shopPaymentHandlerId = $?->getId();

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


  
    // TCMSFieldBoolean
public function isPkgDhlPackstationAllowForPackstation(): bool
{
    return $this->pkgDhlPackstationAllowForPackstation;
}
public function setPkgDhlPackstationAllowForPackstation(bool $pkgDhlPackstationAllowForPackstation): self
{
    $this->pkgDhlPackstationAllowForPackstation = $pkgDhlPackstationAllowForPackstation;

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


  
    // TCMSFieldDecimal
public function getRestrictToBasketValueFrom(): float
{
    return $this->restrictToBasketValueFrom;
}
public function setRestrictToBasketValueFrom(float $restrictToBasketValueFrom): self
{
    $this->restrictToBasketValueFrom = $restrictToBasketValueFrom;

    return $this;
}


  
    // TCMSFieldDecimal
public function getRestrictToBasketValueTo(): float
{
    return $this->restrictToBasketValueTo;
}
public function setRestrictToBasketValueTo(float $restrictToBasketValueTo): self
{
    $this->restrictToBasketValueTo = $restrictToBasketValueTo;

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


  
    // TCMSFieldLookup
public function getShopVat(): \ChameleonSystem\CoreBundle\Entity\ShopVat|null
{
    return $this->shopVat;
}
public function setShopVat(\ChameleonSystem\CoreBundle\Entity\ShopVat|null $shopVat): self
{
    $this->shopVat = $shopVat;
    $this->shopVatId = $shopVat?->getId();

    return $this;
}
public function getShopVatId(): ?string
{
    return $this->shopVatId;
}
public function setShopVatId(?string $shopVatId): self
{
    $this->shopVatId = $shopVatId;
    // todo - load new id
    //$this->shopVatId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getCmsMedia(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->cmsMedia;
}
public function setCmsMedia(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia): self
{
    $this->cmsMedia = $cmsMedia;
    $this->cmsMediaId = $cmsMedia?->getId();

    return $this;
}
public function getCmsMediaId(): ?string
{
    return $this->cmsMediaId;
}
public function setCmsMediaId(?string $cmsMediaId): self
{
    $this->cmsMediaId = $cmsMediaId;
    // todo - load new id
    //$this->cmsMediaId = $?->getId();

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
public function getDataCountryBilling(): \Doctrine\Common\Collections\Collection
{
    return $this->dataCountryBilling;
}
public function setDataCountryBilling(\Doctrine\Common\Collections\Collection $dataCountryBilling): self
{
    $this->dataCountryBilling = $dataCountryBilling;

    return $this;
}


  
    // TCMSFieldBoolean
public function isPositivListLooseMatch(): bool
{
    return $this->positivListLooseMatch;
}
public function setPositivListLooseMatch(bool $positivListLooseMatch): self
{
    $this->positivListLooseMatch = $positivListLooseMatch;

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
public function getShopArticleGroup1Mlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticleGroup1Mlt;
}
public function setShopArticleGroup1Mlt(\Doctrine\Common\Collections\Collection $shopArticleGroup1Mlt): self
{
    $this->shopArticleGroup1Mlt = $shopArticleGroup1Mlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getShopCategory1Mlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopCategory1Mlt;
}
public function setShopCategory1Mlt(\Doctrine\Common\Collections\Collection $shopCategory1Mlt): self
{
    $this->shopCategory1Mlt = $shopCategory1Mlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getShopArticle1Mlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopArticle1Mlt;
}
public function setShopArticle1Mlt(\Doctrine\Common\Collections\Collection $shopArticle1Mlt): self
{
    $this->shopArticle1Mlt = $shopArticle1Mlt;

    return $this;
}


  
}
