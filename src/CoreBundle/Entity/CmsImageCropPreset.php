<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsImageCropPreset {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Width */
    public readonly string $width, 
    /** Height */
    public readonly string $height, 
    /** System name */
    public readonly string $systemName, 
    /** Sort */
    public readonly int $position  ) {}
}