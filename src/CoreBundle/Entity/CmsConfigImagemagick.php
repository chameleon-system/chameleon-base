<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsConfigImagemagick {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Configuration */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsConfig $cmsConfigId, 
    /** Is effective from this image size in pixel */
    public readonly string $fromImageSize, 
    /** Force JPEG. This extends to PNG.  */
    public readonly bool $forceJpeg, 
    /** Quality */
    public readonly string $quality, 
    /** Sharpen */
    public readonly bool $scharpen, 
    /** Gamma correction */
    public readonly float $gamma  ) {}
}