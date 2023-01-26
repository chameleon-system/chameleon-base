<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopWishlistArticle {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to wishlist */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopWishlist $pkgShopWishlistId, 
    /** Created on */
    public readonly \DateTime $datecreated, 
    /** Amount */
    public readonly string $amount, 
    /** Article */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId, 
    /** Comment */
    public readonly string $comment  ) {}
}