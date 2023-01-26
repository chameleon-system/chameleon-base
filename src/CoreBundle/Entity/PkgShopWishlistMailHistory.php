<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopWishlistMailHistory {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to wishlist */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopWishlist $pkgShopWishlistId, 
    /** Email sent on */
    public readonly \DateTime $datesend, 
    /** Recipient name */
    public readonly string $toName, 
    /** Feedback recipient (Email address) */
    public readonly string $toEmail, 
    /** Comment */
    public readonly string $comment  ) {}
}