<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsConfigCmsmoduleExtensions {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Overwritten by */
    public readonly string $newclass, 
    /** Belongs to cms config */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsConfig $cmsConfigId, 
    /** Module to overwrite */
    public readonly string $name, 
    /** Type */
    public readonly string $type  ) {}
}