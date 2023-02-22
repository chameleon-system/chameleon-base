<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopVariantSet;
use ChameleonSystem\CoreBundle\Entity\CmsMedia;
use ChameleonSystem\CoreBundle\Entity\ShopVariantTypeValue;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class ShopVariantType {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var ShopVariantSet|null - Belongs to variant set */
private ?ShopVariantSet $shopVariantSet = null
, 
    // TCMSFieldVarchar
/** @var string - URL name */
private string $urlName = '', 
    // TCMSFieldLookup
/** @var CmsMedia|null - Image or icon for variant type (optional) */
private ?CmsMedia $cmsMedia = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, ShopVariantTypeValue> - Available variant values */
private Collection $shopVariantTypeValueCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Identifier */
private string $identifier = ''  ) {}

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
public function getShopVariantSet(): ?ShopVariantSet
{
    return $this->shopVariantSet;
}

public function setShopVariantSet(?ShopVariantSet $shopVariantSet): self
{
    $this->shopVariantSet = $shopVariantSet;

    return $this;
}


  
    // TCMSFieldVarchar
public function getUrlName(): string
{
    return $this->urlName;
}
public function setUrlName(string $urlName): self
{
    $this->urlName = $urlName;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsMedia(): ?CmsMedia
{
    return $this->cmsMedia;
}

public function setCmsMedia(?CmsMedia $cmsMedia): self
{
    $this->cmsMedia = $cmsMedia;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ShopVariantTypeValue>
*/
public function getShopVariantTypeValueCollection(): Collection
{
    return $this->shopVariantTypeValueCollection;
}

public function addShopVariantTypeValueCollection(ShopVariantTypeValue $shopVariantTypeValue): self
{
    if (!$this->shopVariantTypeValueCollection->contains($shopVariantTypeValue)) {
        $this->shopVariantTypeValueCollection->add($shopVariantTypeValue);
        $shopVariantTypeValue->setShopVariantType($this);
    }

    return $this;
}

public function removeShopVariantTypeValueCollection(ShopVariantTypeValue $shopVariantTypeValue): self
{
    if ($this->shopVariantTypeValueCollection->removeElement($shopVariantTypeValue)) {
        // set the owning side to null (unless already changed)
        if ($shopVariantTypeValue->getShopVariantType() === $this) {
            $shopVariantTypeValue->setShopVariantType(null);
        }
    }

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
