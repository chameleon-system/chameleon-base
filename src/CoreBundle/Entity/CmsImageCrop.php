<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsImageCrop {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaId, 
    /** Preset */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsImageCropPreset $cmsImageCropPresetId, 
    /** X position of crop */
    public readonly string $posX, 
    /** Y position of crop */
    public readonly string $posY, 
    /**  */
    public readonly string $width, 
    /** Crop height */
    public readonly string $height, 
    /** Name */
    public readonly string $name  ) {}
}