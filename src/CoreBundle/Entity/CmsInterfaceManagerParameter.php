<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsInterfaceManagerParameter {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to interface */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsInterfaceManager $cmsInterfaceManagerId, 
    /** Description */
    public readonly string $description, 
    /** Name */
    public readonly string $name, 
    /** Value */
    public readonly string $value  ) {}
}