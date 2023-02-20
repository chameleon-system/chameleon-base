<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopArticleCatalogConf;

class ShopArticleCatalogConfDefaultOrder {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var ShopArticleCatalogConf|null - Belongs to configuration */
private ?ShopArticleCatalogConf $shopArticleCatalogConf = null
, 
    // TCMSFieldVarchar
/** @var string - Name (description) */
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
    // TCMSFieldLookupParentID
public function getShopArticleCatalogConf(): ?ShopArticleCatalogConf
{
    return $this->shopArticleCatalogConf;
}

public function setShopArticleCatalogConf(?ShopArticleCatalogConf $shopArticleCatalogConf): self
{
    $this->shopArticleCatalogConf = $shopArticleCatalogConf;

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
