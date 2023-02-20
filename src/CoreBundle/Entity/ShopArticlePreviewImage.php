<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticlePreviewImage {
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
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleImageSize|null - Preview image size / type */
private \ChameleonSystem\CoreBundle\Entity\ShopArticleImageSize|null $shopArticleImageSize = null,
/** @var null|string - Preview image size / type */
private ?string $shopArticleImageSizeId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Preview image */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia = null,
/** @var null|string - Preview image */
private ?string $cmsMediaId = null
  ) {}

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
public function getShopArticleImageSize(): \ChameleonSystem\CoreBundle\Entity\ShopArticleImageSize|null
{
    return $this->shopArticleImageSize;
}
public function setShopArticleImageSize(\ChameleonSystem\CoreBundle\Entity\ShopArticleImageSize|null $shopArticleImageSize): self
{
    $this->shopArticleImageSize = $shopArticleImageSize;
    $this->shopArticleImageSizeId = $shopArticleImageSize?->getId();

    return $this;
}
public function getShopArticleImageSizeId(): ?string
{
    return $this->shopArticleImageSizeId;
}
public function setShopArticleImageSizeId(?string $shopArticleImageSizeId): self
{
    $this->shopArticleImageSizeId = $shopArticleImageSizeId;
    // todo - load new id
    //$this->shopArticleImageSizeId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getCmsMedia(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->cmsMedia;
}
public function setCmsMedia(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia): self
{
    $this->cmsMedia = $cmsMedia;
    $this->cmsMediaId = $cmsMedia?->getId();

    return $this;
}
public function getCmsMediaId(): ?string
{
    return $this->cmsMediaId;
}
public function setCmsMediaId(?string $cmsMediaId): self
{
    $this->cmsMediaId = $cmsMediaId;
    // todo - load new id
    //$this->cmsMediaId = $?->getId();

    return $this;
}



  
}
