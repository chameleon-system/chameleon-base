<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMenuCategory {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Position */
    public readonly int $position, 
    /** System name */
    public readonly string $systemName, 
    /**  */
    public readonly string $iconFontCssClass, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsMenuItem[] Menu items */
    public readonly array $cmsMenuItem  ) {}
}