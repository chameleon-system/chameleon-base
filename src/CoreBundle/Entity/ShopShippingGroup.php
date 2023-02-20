<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopShippingGroup {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopShippingGroupHandler|null - Shipping group handler */
private \ChameleonSystem\CoreBundle\Entity\ShopShippingGroupHandler|null $shopShippingGroupHandler = null,
/** @var null|string - Shipping group handler */
private ?string $shopShippingGroupHandlerId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVat|null - VAT group */
private \ChameleonSystem\CoreBundle\Entity\ShopVat|null $shopVat = null,
/** @var null|string - VAT group */
private ?string $shopVatId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0, 
    // TCMSFieldBoolean
/** @var bool - Active */
private bool $active = false, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Active from */
private \DateTime|null $activeFrom = null, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Active until */
private \DateTime|null $activeTo = null, 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser[] - Restrict to following customers */
private \Doctrine\Common\Collections\Collection $dataExtranetUserMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] - Restrict to following customer groups */
private \Doctrine\Common\Collections\Collection $dataExtranetGroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopShippingType[] - Shipping types */
private \Doctrine\Common\Collections\Collection $shopShippingTypeMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopPaymentMethod[] - Payment methods */
private \Doctrine\Common\Collections\Collection $shopPaymentMethodMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\ShopShippingGroup[] - Is displayed only if the following shipping groups are not available */
private \Doctrine\Common\Collections\Collection $shopShippingGroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] - Restrict to the following portals */
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


  
    // TCMSFieldLookup
public function getShopShippingGroupHandler(): \ChameleonSystem\CoreBundle\Entity\ShopShippingGroupHandler|null
{
    return $this->shopShippingGroupHandler;
}
public function setShopShippingGroupHandler(\ChameleonSystem\CoreBundle\Entity\ShopShippingGroupHandler|null $shopShippingGroupHandler): self
{
    $this->shopShippingGroupHandler = $shopShippingGroupHandler;
    $this->shopShippingGroupHandlerId = $shopShippingGroupHandler?->getId();

    return $this;
}
public function getShopShippingGroupHandlerId(): ?string
{
    return $this->shopShippingGroupHandlerId;
}
public function setShopShippingGroupHandlerId(?string $shopShippingGroupHandlerId): self
{
    $this->shopShippingGroupHandlerId = $shopShippingGroupHandlerId;
    // todo - load new id
    //$this->shopShippingGroupHandlerId = $?->getId();

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



  
    // TCMSFieldLookupMultiselect
public function getShopShippingTypeMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopShippingTypeMlt;
}
public function setShopShippingTypeMlt(\Doctrine\Common\Collections\Collection $shopShippingTypeMlt): self
{
    $this->shopShippingTypeMlt = $shopShippingTypeMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getShopPaymentMethodMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopPaymentMethodMlt;
}
public function setShopPaymentMethodMlt(\Doctrine\Common\Collections\Collection $shopPaymentMethodMlt): self
{
    $this->shopPaymentMethodMlt = $shopPaymentMethodMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getShopShippingGroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->shopShippingGroupMlt;
}
public function setShopShippingGroupMlt(\Doctrine\Common\Collections\Collection $shopShippingGroupMlt): self
{
    $this->shopShippingGroupMlt = $shopShippingGroupMlt;

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
