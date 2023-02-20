<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopVariantSet {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVariantDisplayHandler|null - Display handler for variant selection in  shop */
private \ChameleonSystem\CoreBundle\Entity\ShopVariantDisplayHandler|null $shopVariantDisplayHandler = null,
/** @var null|string - Display handler for variant selection in  shop */
private ?string $shopVariantDisplayHandlerId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVariantType[] - Variant types of variant set */
private \Doctrine\Common\Collections\Collection $shopVariantTypeCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectCheckboxesSelectFieldsFromTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsFieldConf[] - Fields of variant which may differ from parent item */
private \Doctrine\Common\Collections\Collection $cmsFieldConfMlt = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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


  
    // TCMSFieldPropertyTable
public function getShopVariantTypeCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->shopVariantTypeCollection;
}
public function setShopVariantTypeCollection(\Doctrine\Common\Collections\Collection $shopVariantTypeCollection): self
{
    $this->shopVariantTypeCollection = $shopVariantTypeCollection;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxesSelectFieldsFromTable
public function getCmsFieldConfMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsFieldConfMlt;
}
public function setCmsFieldConfMlt(\Doctrine\Common\Collections\Collection $cmsFieldConfMlt): self
{
    $this->cmsFieldConfMlt = $cmsFieldConfMlt;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopVariantDisplayHandler(): \ChameleonSystem\CoreBundle\Entity\ShopVariantDisplayHandler|null
{
    return $this->shopVariantDisplayHandler;
}
public function setShopVariantDisplayHandler(\ChameleonSystem\CoreBundle\Entity\ShopVariantDisplayHandler|null $shopVariantDisplayHandler): self
{
    $this->shopVariantDisplayHandler = $shopVariantDisplayHandler;
    $this->shopVariantDisplayHandlerId = $shopVariantDisplayHandler?->getId();

    return $this;
}
public function getShopVariantDisplayHandlerId(): ?string
{
    return $this->shopVariantDisplayHandlerId;
}
public function setShopVariantDisplayHandlerId(?string $shopVariantDisplayHandlerId): self
{
    $this->shopVariantDisplayHandlerId = $shopVariantDisplayHandlerId;
    // todo - load new id
    //$this->shopVariantDisplayHandlerId = $?->getId();

    return $this;
}



  
}
