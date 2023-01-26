<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMenuCustomItem {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Target URL */
    public readonly string $url, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsRight[] Required rights */
    public readonly array $cmsRightMlt  ) {}
}