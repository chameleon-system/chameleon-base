<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopWishlist {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to user */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId, 
    /** Description stored by the user */
    public readonly string $description, 
    /** Public */
    public readonly bool $isPublic, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgShopWishlistArticle[] Wishlist articles */
    public readonly array $pkgShopWishlistArticle, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgShopWishlistMailHistory[] Wishlist mail history */
    public readonly array $pkgShopWishlistMailHistory  ) {}
}