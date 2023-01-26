<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMediaTree {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Directoy name */
    public readonly string $name, 
    /** Icon */
    public readonly string $icon, 
    /** URL path to the image */
    public readonly string $pathCache, 
    /** Is subitem of */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMediaTree $parentId, 
    /** Position */
    public readonly string $entrySort  ) {}
}