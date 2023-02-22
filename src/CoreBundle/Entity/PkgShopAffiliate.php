<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\Shop;
use ChameleonSystem\CoreBundle\Entity\PkgShopAffiliateParameter;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class PkgShopAffiliate {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var Shop|null - Belongs to shop */
private ?Shop $shop = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - URL parameter used to transfer the tracking code */
private string $urlParameterName = '', 
    // TCMSFieldVarchar
/** @var string - Seconds, for which the code is still valid with inactive session */
private string $numberOfSecondsValid = '0', 
    // TCMSFieldVarchar
/** @var string - Class */
private string $class = '', 
    // TCMSFieldVarchar
/** @var string - Class subtype (path relative to ./classes) */
private string $classSubtype = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, PkgShopAffiliateParameter> - Parameter */
private Collection $pkgShopAffiliateParameterCollection = new ArrayCollection()
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
public function getShop(): ?Shop
{
    return $this->shop;
}

public function setShop(?Shop $shop): self
{
    $this->shop = $shop;

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
public function getUrlParameterName(): string
{
    return $this->urlParameterName;
}
public function setUrlParameterName(string $urlParameterName): self
{
    $this->urlParameterName = $urlParameterName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getNumberOfSecondsValid(): string
{
    return $this->numberOfSecondsValid;
}
public function setNumberOfSecondsValid(string $numberOfSecondsValid): self
{
    $this->numberOfSecondsValid = $numberOfSecondsValid;

    return $this;
}


  
    // TCMSFieldVarchar
public function getClass(): string
{
    return $this->class;
}
public function setClass(string $class): self
{
    $this->class = $class;

    return $this;
}


  
    // TCMSFieldVarchar
public function getClassSubtype(): string
{
    return $this->classSubtype;
}
public function setClassSubtype(string $classSubtype): self
{
    $this->classSubtype = $classSubtype;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, PkgShopAffiliateParameter>
*/
public function getPkgShopAffiliateParameterCollection(): Collection
{
    return $this->pkgShopAffiliateParameterCollection;
}

public function addPkgShopAffiliateParameterCollection(PkgShopAffiliateParameter $pkgShopAffiliateParameter): self
{
    if (!$this->pkgShopAffiliateParameterCollection->contains($pkgShopAffiliateParameter)) {
        $this->pkgShopAffiliateParameterCollection->add($pkgShopAffiliateParameter);
        $pkgShopAffiliateParameter->setPkgShopAffiliate($this);
    }

    return $this;
}

public function removePkgShopAffiliateParameterCollection(PkgShopAffiliateParameter $pkgShopAffiliateParameter): self
{
    if ($this->pkgShopAffiliateParameterCollection->removeElement($pkgShopAffiliateParameter)) {
        // set the owning side to null (unless already changed)
        if ($pkgShopAffiliateParameter->getPkgShopAffiliate() === $this) {
            $pkgShopAffiliateParameter->setPkgShopAffiliate(null);
        }
    }

    return $this;
}


  
}
