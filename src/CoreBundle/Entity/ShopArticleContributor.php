<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopArticle;
use ChameleonSystem\CoreBundle\Entity\ShopContributor;
use ChameleonSystem\CoreBundle\Entity\ShopContributorType;

class ShopArticleContributor {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var ShopArticle|null - Belongs to article */
private ?ShopArticle $shopArticle = null
, 
    // TCMSFieldLookup
/** @var ShopContributor|null - Contributing person */
private ?ShopContributor $shopContributor = null
, 
    // TCMSFieldLookup
/** @var ShopContributorType|null - Role of the contributing person / contribution type */
private ?ShopContributorType $shopContributorType = null
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
public function getShopArticle(): ?ShopArticle
{
    return $this->shopArticle;
}

public function setShopArticle(?ShopArticle $shopArticle): self
{
    $this->shopArticle = $shopArticle;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopContributor(): ?ShopContributor
{
    return $this->shopContributor;
}

public function setShopContributor(?ShopContributor $shopContributor): self
{
    $this->shopContributor = $shopContributor;

    return $this;
}


  
    // TCMSFieldLookup
public function getShopContributorType(): ?ShopContributorType
{
    return $this->shopContributorType;
}

public function setShopContributorType(?ShopContributorType $shopContributorType): self
{
    $this->shopContributorType = $shopContributorType;

    return $this;
}


  
}
