<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMasterPagedefSpot {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to the CMS page template */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedef $cmsMasterPagedefId, 
    /** Belongs to theme block */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlock $pkgCmsThemeBlockId, 
    /** Name */
    public readonly string $name, 
    /** Model (class name) */
    public readonly string $model, 
    /** Module view */
    public readonly string $view, 
    /** Static */
    public readonly bool $static, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpotParameter[] Parameter */
    public readonly array $cmsMasterPagedefSpotParameter, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpotAccess[] Spot restrictions */
    public readonly array $cmsMasterPagedefSpotAccess  ) {}
}