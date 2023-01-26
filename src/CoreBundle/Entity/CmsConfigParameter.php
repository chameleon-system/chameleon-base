<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsConfigParameter {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to CMS config */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsConfig $cmsConfigId, 
    /** System name */
    public readonly string $systemname, 
    /** Name / description */
    public readonly string $name, 
    /** Value */
    public readonly string $value  ) {}
}