<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsFontImage {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Profile name */
    public readonly string $profileName, 
    /** Image height */
    public readonly string $imgHeight, 
    /** Image width */
    public readonly string $imgWidth, 
    /** Image background color */
    public readonly string $imgBackgroundColor, 
    /** Font color */
    public readonly string $fontColor, 
    /** Font size */
    public readonly string $fontSize, 
    /** Font width */
    public readonly string $fontWeight, 
    /** Font alignment */
    public readonly string $fontAlign, 
    /** Font alignment vertical */
    public readonly string $fontVerticalAlign, 
    /** Font file */
    public readonly string $fontFilename, 
    /** Image type */
    public readonly string $imgType, 
    /** With background image */
    public readonly bool $imgBackgroundImg, 
    /** Background image file */
    public readonly string $backgroundImgFile, 
    /** Text position X-axis */
    public readonly string $textPositionX, 
    /** Text position Y-axis */
    public readonly string $textPositionY  ) {}
}