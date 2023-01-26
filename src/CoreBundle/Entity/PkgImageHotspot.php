<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgImageHotspot {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Headline */
    public readonly string $name, 
    /** How long should an image be displayed (in seconds)? */
    public readonly string $autoSlideTime, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgImageHotspotItem[] Images */
    public readonly array $pkgImageHotspotItem  ) {}
}