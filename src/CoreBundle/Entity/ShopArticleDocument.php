<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleDocument {
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
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleDocumentType|null - Article document type */
private \ChameleonSystem\CoreBundle\Entity\ShopArticleDocumentType|null $shopArticleDocumentType = null,
/** @var null|string - Article document type */
private ?string $shopArticleDocumentTypeId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsDocument|null - Document */
private \ChameleonSystem\CoreBundle\Entity\CmsDocument|null $cmsDocument = null,
/** @var null|string - Document */
private ?string $cmsDocumentId = null
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
public function getShopArticleDocumentType(): \ChameleonSystem\CoreBundle\Entity\ShopArticleDocumentType|null
{
    return $this->shopArticleDocumentType;
}
public function setShopArticleDocumentType(\ChameleonSystem\CoreBundle\Entity\ShopArticleDocumentType|null $shopArticleDocumentType): self
{
    $this->shopArticleDocumentType = $shopArticleDocumentType;
    $this->shopArticleDocumentTypeId = $shopArticleDocumentType?->getId();

    return $this;
}
public function getShopArticleDocumentTypeId(): ?string
{
    return $this->shopArticleDocumentTypeId;
}
public function setShopArticleDocumentTypeId(?string $shopArticleDocumentTypeId): self
{
    $this->shopArticleDocumentTypeId = $shopArticleDocumentTypeId;
    // todo - load new id
    //$this->shopArticleDocumentTypeId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getCmsDocument(): \ChameleonSystem\CoreBundle\Entity\CmsDocument|null
{
    return $this->cmsDocument;
}
public function setCmsDocument(\ChameleonSystem\CoreBundle\Entity\CmsDocument|null $cmsDocument): self
{
    $this->cmsDocument = $cmsDocument;
    $this->cmsDocumentId = $cmsDocument?->getId();

    return $this;
}
public function getCmsDocumentId(): ?string
{
    return $this->cmsDocumentId;
}
public function setCmsDocumentId(?string $cmsDocumentId): self
{
    $this->cmsDocumentId = $cmsDocumentId;
    // todo - load new id
    //$this->cmsDocumentId = $?->getId();

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
