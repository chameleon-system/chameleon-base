<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgImageHotspotItemMarker {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to hotspot image */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItem $pkgImageHotspotItemId, 
    /** Alt or link text of the image */
    public readonly string $name, 
    /** Position of top border relative to top border of background image */
    public readonly string $top, 
    /** Position of left border relative to left border of background image */
    public readonly string $left, 
    /** Link to object */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplPage|\ChameleonSystem\CoreBundle\Entity\ShopCategory|\ChameleonSystem\CoreBundle\Entity\ShopArticle $linkedRecord, 
    /** Alternative link */
    public readonly string $url, 
    /** Show object layover */
    public readonly bool $showObjectLayover, 
    /** Image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaId, 
    /** Hover image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaHoverId  ) {}
}