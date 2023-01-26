<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrderStep {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Internal name */
    public readonly string $systemname, 
    /** URL name */
    public readonly string $urlName, 
    /** Headline */
    public readonly string $name, 
    /** Show in navigation list */
    public readonly bool $showInNavigation, 
    /** Description */
    public readonly string $description, 
    /** Position */
    public readonly int $position, 
    /** Class name */
    public readonly string $class, 
    /** Class type */
    public readonly string $classType, 
    /** Class subtype */
    public readonly string $classSubtype, 
    /** View to use for the step */
    public readonly string $renderViewName, 
    /** View type */
    public readonly string $renderViewType, 
    /** CSS icon class inactive */
    public readonly string $cssIconClassInactive, 
    /** CSS icon class active */
    public readonly string $cssIconClassActive, 
    /** Use template */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $templateNodeCmsTreeId  ) {}
}