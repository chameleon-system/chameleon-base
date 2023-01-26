<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgImageHotspotItemSpot {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to hotspot image */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItem $pkgImageHotspotItemId, 
    /** Distance top */
    public readonly string $top, 
    /** Distance left */
    public readonly string $left, 
    /** Hotspot icon type */
    public readonly string $hotspotType, 
    /** Linked CMS object */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle|\ChameleonSystem\CoreBundle\Entity\ShopCategory|\ChameleonSystem\CoreBundle\Entity\CmsTplPage $linkedRecord, 
    /** External URL */
    public readonly string $externalUrl, 
    /** Polygon area */
    public readonly string $polygonArea, 
    /** Show product info layover */
    public readonly bool $showSpot  ) {}
}