<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\ShopArticle;

class ShopArticlePreviewImage {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var ShopArticle|null - Belongs to article */
private ?ShopArticle $shopArticle = null
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
    // TCMSFieldLookupParentID
public function getShopArticle(): ?ShopArticle
{
    return $this->shopArticle;
}

public function setShopArticle(?ShopArticle $shopArticle): self
{
    $this->shopArticle = $shopArticle;

    return $this;
}


  
}
