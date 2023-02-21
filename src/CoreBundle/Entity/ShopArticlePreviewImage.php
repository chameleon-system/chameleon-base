<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopArticle;
use ChameleonSystem\CoreBundle\Entity\ShopArticleImageSize;
use ChameleonSystem\CoreBundle\Entity\CmsMedia;

class ShopArticlePreviewImage {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var ShopArticle|null - Belongs to article */
private ?ShopArticle $shopArticle = null
, 
    // TCMSFieldLookup
/** @var ShopArticleImageSize|null - Preview image size / type */
private ?ShopArticleImageSize $shopArticleImageSize = null
, 
    // TCMSFieldLookup
/** @var CmsMedia|null - Preview image */
private ?CmsMedia $cmsMedia = null
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
public function getShopArticleImageSize(): ?ShopArticleImageSize
{
    return $this->shopArticleImageSize;
}

public function setShopArticleImageSize(?ShopArticleImageSize $shopArticleImageSize): self
{
    $this->shopArticleImageSize = $shopArticleImageSize;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsMedia(): ?CmsMedia
{
    return $this->cmsMedia;
}

public function setCmsMedia(?CmsMedia $cmsMedia): self
{
    $this->cmsMedia = $cmsMedia;

    return $this;
}


  
}
