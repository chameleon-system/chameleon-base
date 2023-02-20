<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopVariantType {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVariantSet|null - Belongs to variant set */
private \ChameleonSystem\CoreBundle\Entity\ShopVariantSet|null $shopVariantSet = null,
/** @var null|string - Belongs to variant set */
private ?string $shopVariantSetId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Image or icon for variant type (optional) */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia = null,
/** @var null|string - Image or icon for variant type (optional) */
private ?string $cmsMediaId = null
, 
    // TCMSFieldSEOURLTitle
/** @var string - URL name */
private string $urlName = '', 
    // TCMSFieldPosition
/** @var int - Sorting */
private int $position = 0, 
    // TCMSFieldOption
/** @var string - Input type of variant values in the CMS */
private string $valueSelectType = 'SelectBox', 
    // TCMSFieldTablefieldname
/** @var string - Order values by */
private string $shopVariantTypeValueCmsfieldname = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVariantTypeValue[] - Available variant values */
private \Doctrine\Common\Collections\Collection $shopVariantTypeValueCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Identifier */
private string $identifier = ''  ) {}

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
public function getShopVariantSet(): \ChameleonSystem\CoreBundle\Entity\ShopVariantSet|null
{
    return $this->shopVariantSet;
}
public function setShopVariantSet(\ChameleonSystem\CoreBundle\Entity\ShopVariantSet|null $shopVariantSet): self
{
    $this->shopVariantSet = $shopVariantSet;
    $this->shopVariantSetId = $shopVariantSet?->getId();

    return $this;
}
public function getShopVariantSetId(): ?string
{
    return $this->shopVariantSetId;
}
public function setShopVariantSetId(?string $shopVariantSetId): self
{
    $this->shopVariantSetId = $shopVariantSetId;
    // todo - load new id
    //$this->shopVariantSetId = $?->getId();

    return $this;
}



  
    // TCMSFieldSEOURLTitle
public function getUrlName(): string
{
    return $this->urlName;
}
public function setUrlName(string $urlName): self
{
    $this->urlName = $urlName;

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



  
    // TCMSFieldOption
public function getValueSelectType(): string
{
    return $this->valueSelectType;
}
public function setValueSelectType(string $valueSelectType): self
{
    $this->valueSelectType = $valueSelectType;

    return $this;
}


  
    // TCMSFieldTablefieldname
public function getShopVariantTypeValueCmsfieldname(): string
{
    return $this->shopVariantTypeValueCmsfieldname;
}
public function setShopVariantTypeValueCmsfieldname(string $shopVariantTypeValueCmsfieldname): self
{
    $this->shopVariantTypeValueCmsfieldname = $shopVariantTypeValueCmsfieldname;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getShopVariantTypeValueCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopVariantTypeValueCollection;
}
public function setShopVariantTypeValueCollection(\Doctrine\Common\Collections\Collection $shopVariantTypeValueCollection): self
{
    $this->shopVariantTypeValueCollection = $shopVariantTypeValueCollection;

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
public function getIdentifier(): string
{
    return $this->identifier;
}
public function setIdentifier(string $identifier): self
{
    $this->identifier = $identifier;

    return $this;
}


  
}
