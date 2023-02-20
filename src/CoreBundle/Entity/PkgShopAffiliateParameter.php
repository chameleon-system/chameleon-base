<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopAffiliateParameter {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgShopAffiliate|null - Belongs to affiliate program */
private \ChameleonSystem\CoreBundle\Entity\PkgShopAffiliate|null $pkgShopAffiliate = null,
/** @var null|string - Belongs to affiliate program */
private ?string $pkgShopAffiliateId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldText
/** @var string - Value */
private string $value = ''  ) {}

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
public function getPkgShopAffiliate(): \ChameleonSystem\CoreBundle\Entity\PkgShopAffiliate|null
{
    return $this->pkgShopAffiliate;
}
public function setPkgShopAffiliate(\ChameleonSystem\CoreBundle\Entity\PkgShopAffiliate|null $pkgShopAffiliate): self
{
    $this->pkgShopAffiliate = $pkgShopAffiliate;
    $this->pkgShopAffiliateId = $pkgShopAffiliate?->getId();

    return $this;
}
public function getPkgShopAffiliateId(): ?string
{
    return $this->pkgShopAffiliateId;
}
public function setPkgShopAffiliateId(?string $pkgShopAffiliateId): self
{
    $this->pkgShopAffiliateId = $pkgShopAffiliateId;
    // todo - load new id
    //$this->pkgShopAffiliateId = $?->getId();

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


  
    // TCMSFieldText
public function getValue(): string
{
    return $this->value;
}
public function setValue(string $value): self
{
    $this->value = $value;

    return $this;
}


  
}
