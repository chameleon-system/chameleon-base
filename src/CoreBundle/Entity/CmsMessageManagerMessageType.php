<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMessageManagerMessageType {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Systemname */
    public readonly string $systemname, 
    /** Color */
    public readonly string $color, 
    /** Icon */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaId, 
    /** Class name */
    public readonly string $class  ) {}
}