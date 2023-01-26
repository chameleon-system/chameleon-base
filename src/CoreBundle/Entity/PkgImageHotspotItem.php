<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgImageHotspotItem {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to image hotspot */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgImageHotspot $pkgImageHotspotId, 
    /** Alternative text for image */
    public readonly string $name, 
    /** Active */
    public readonly bool $active, 
    /** Position */
    public readonly int $position, 
    /** Image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaId
    /** Image - cropped image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia cmsMediaIdImageCropId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItemSpot[] Hotspots and linked areas */
    public readonly array $pkgImageHotspotItemSpot, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItemMarker[] Hotspots with image */
    public readonly array $pkgImageHotspotItemMarker  ) {}
}