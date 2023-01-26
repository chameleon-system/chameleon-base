<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopWishlistOrderItem {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Wishlist */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopWishlist $pkgShopWishlistId, 
    /** Order item */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopOrderItem $shopOrderItemId, 
    /** Wishlist owner */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId, 
    /** Email of the wishlist owner */
    public readonly string $dataExtranetUserEmail  ) {}
}