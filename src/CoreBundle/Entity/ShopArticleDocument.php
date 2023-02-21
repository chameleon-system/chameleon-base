<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopArticle;
use ChameleonSystem\CoreBundle\Entity\ShopArticleDocumentType;
use ChameleonSystem\CoreBundle\Entity\CmsDocument;

class ShopArticleDocument {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var ShopArticle|null - Belongs to article */
private ?ShopArticle $shopArticle = null
, 
    // TCMSFieldLookup
/** @var ShopArticleDocumentType|null - Article document type */
private ?ShopArticleDocumentType $shopArticleDocumentType = null
, 
    // TCMSFieldLookup
/** @var CmsDocument|null - Document */
private ?CmsDocument $cmsDocument = null
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
public function getShopArticleDocumentType(): ?ShopArticleDocumentType
{
    return $this->shopArticleDocumentType;
}

public function setShopArticleDocumentType(?ShopArticleDocumentType $shopArticleDocumentType): self
{
    $this->shopArticleDocumentType = $shopArticleDocumentType;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsDocument(): ?CmsDocument
{
    return $this->cmsDocument;
}

public function setCmsDocument(?CmsDocument $cmsDocument): self
{
    $this->cmsDocument = $cmsDocument;

    return $this;
}


  
}
