<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgShopAffiliate;

class PkgShopAffiliateParameter {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var PkgShopAffiliate|null - Belongs to affiliate program */
private ?PkgShopAffiliate $pkgShopAffiliate = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = ''  ) {}

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
public function getPkgShopAffiliate(): ?PkgShopAffiliate
{
    return $this->pkgShopAffiliate;
}

public function setPkgShopAffiliate(?PkgShopAffiliate $pkgShopAffiliate): self
{
    $this->pkgShopAffiliate = $pkgShopAffiliate;

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


  
}
