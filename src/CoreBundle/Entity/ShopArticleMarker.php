<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleMarker {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** System name */
    public readonly string $name, 
    /** Title (as shown on the website) */
    public readonly string $title, 
    /** Icon */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaId, 
    /** Description */
    public readonly string $description  ) {}
}