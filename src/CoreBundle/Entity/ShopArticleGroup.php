<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleGroup {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopVat|null - VAT group */
private \ChameleonSystem\CoreBundle\Entity\ShopVat|null $shopVat = null,
/** @var null|string - VAT group */
private ?string $shopVatId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = ''  ) {}

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



  
}
