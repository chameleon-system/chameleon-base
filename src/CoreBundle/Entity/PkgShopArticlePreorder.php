<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopArticlePreorder {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\ShopArticle|null - Preordered product */
private \ChameleonSystem\CoreBundle\Entity\ShopArticle|null $shopArticle = null,
/** @var null|string - Preordered product */
private ?string $shopArticleId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Belongs to portal */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Belongs to portal */
private ?string $cmsPortalId = null
, 
    // TCMSFieldEmail
/** @var string - Email address */
private string $preorderUserEmail = '', 
    // TCMSFieldDateTime
/** @var \DateTime|null - Date */
private \DateTime|null $preorderDate = null  ) {}

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



  
    // TCMSFieldEmail
public function getPreorderUserEmail(): string
{
    return $this->preorderUserEmail;
}
public function setPreorderUserEmail(string $preorderUserEmail): self
{
    $this->preorderUserEmail = $preorderUserEmail;

    return $this;
}


  
    // TCMSFieldDateTime
public function getPreorderDate(): \DateTime|null
{
    return $this->preorderDate;
}
public function setPreorderDate(\DateTime|null $preorderDate): self
{
    $this->preorderDate = $preorderDate;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsPortal(): \ChameleonSystem\CoreBundle\Entity\CmsPortal|null
{
    return $this->cmsPortal;
}
public function setCmsPortal(\ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;
    $this->cmsPortalId = $cmsPortal?->getId();

    return $this;
}
public function getCmsPortalId(): ?string
{
    return $this->cmsPortalId;
}
public function setCmsPortalId(?string $cmsPortalId): self
{
    $this->cmsPortalId = $cmsPortalId;
    // todo - load new id
    //$this->cmsPortalId = $?->getId();

    return $this;
}



  
}
