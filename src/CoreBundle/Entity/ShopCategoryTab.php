<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopCategoryTab {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopCategory|null - Belongs to category */
private \ChameleonSystem\CoreBundle\Entity\ShopCategory|null $shopCategory = null,
/** @var null|string - Belongs to category */
private ?string $shopCategoryId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldWYSIWYG
/** @var string - Description */
private string $description = ''  ) {}

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
public function getShopCategory(): \ChameleonSystem\CoreBundle\Entity\ShopCategory|null
{
    return $this->shopCategory;
}
public function setShopCategory(\ChameleonSystem\CoreBundle\Entity\ShopCategory|null $shopCategory): self
{
    $this->shopCategory = $shopCategory;
    $this->shopCategoryId = $shopCategory?->getId();

    return $this;
}
public function getShopCategoryId(): ?string
{
    return $this->shopCategoryId;
}
public function setShopCategoryId(?string $shopCategoryId): self
{
    $this->shopCategoryId = $shopCategoryId;
    // todo - load new id
    //$this->shopCategoryId = $?->getId();

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


  
}
