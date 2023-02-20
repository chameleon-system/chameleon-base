<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleContributor {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle|null - Belongs to article */
private \ChameleonSystem\CoreBundle\Entity\ShopArticle|null $shopArticle = null,
/** @var null|string - Belongs to article */
private ?string $shopArticleId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopContributor|null - Contributing person */
private \ChameleonSystem\CoreBundle\Entity\ShopContributor|null $shopContributor = null,
/** @var null|string - Contributing person */
private ?string $shopContributorId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopContributorType|null - Role of the contributing person / contribution type */
private \ChameleonSystem\CoreBundle\Entity\ShopContributorType|null $shopContributorType = null,
/** @var null|string - Role of the contributing person / contribution type */
private ?string $shopContributorTypeId = null
, 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0  ) {}

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
public function getShopArticle(): \ChameleonSystem\CoreBundle\Entity\ShopArticle|null
{
    return $this->shopArticle;
}
public function setShopArticle(\ChameleonSystem\CoreBundle\Entity\ShopArticle|null $shopArticle): self
{
    $this->shopArticle = $shopArticle;
    $this->shopArticleId = $shopArticle?->getId();

    return $this;
}
public function getShopArticleId(): ?string
{
    return $this->shopArticleId;
}
public function setShopArticleId(?string $shopArticleId): self
{
    $this->shopArticleId = $shopArticleId;
    // todo - load new id
    //$this->shopArticleId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getShopContributor(): \ChameleonSystem\CoreBundle\Entity\ShopContributor|null
{
    return $this->shopContributor;
}
public function setShopContributor(\ChameleonSystem\CoreBundle\Entity\ShopContributor|null $shopContributor): self
{
    $this->shopContributor = $shopContributor;
    $this->shopContributorId = $shopContributor?->getId();

    return $this;
}
public function getShopContributorId(): ?string
{
    return $this->shopContributorId;
}
public function setShopContributorId(?string $shopContributorId): self
{
    $this->shopContributorId = $shopContributorId;
    // todo - load new id
    //$this->shopContributorId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getShopContributorType(): \ChameleonSystem\CoreBundle\Entity\ShopContributorType|null
{
    return $this->shopContributorType;
}
public function setShopContributorType(\ChameleonSystem\CoreBundle\Entity\ShopContributorType|null $shopContributorType): self
{
    $this->shopContributorType = $shopContributorType;
    $this->shopContributorTypeId = $shopContributorType?->getId();

    return $this;
}
public function getShopContributorTypeId(): ?string
{
    return $this->shopContributorTypeId;
}
public function setShopContributorTypeId(?string $shopContributorTypeId): self
{
    $this->shopContributorTypeId = $shopContributorTypeId;
    // todo - load new id
    //$this->shopContributorTypeId = $?->getId();

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


  
}
