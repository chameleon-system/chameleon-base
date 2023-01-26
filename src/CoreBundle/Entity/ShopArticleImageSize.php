<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleImageSize {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to shop */
    public readonly \ChameleonSystem\CoreBundle\Entity\Shop $shopId, 
    /** System name */
    public readonly string $nameInternal, 
    /** Name */
    public readonly string $name, 
    /** Width */
    public readonly string $width, 
    /** Height */
    public readonly string $height, 
    /** Force size */
    public readonly bool $forceSize  ) {}
}