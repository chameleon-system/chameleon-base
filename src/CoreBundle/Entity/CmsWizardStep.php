<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsWizardStep {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** CMS display name */
    public readonly string $displayName, 
    /** Title / headline */
    public readonly string $name, 
    /** Description */
    public readonly string $description, 
    /** Internal name */
    public readonly string $systemname, 
    /** Position */
    public readonly int $position, 
    /** URL name */
    public readonly string $urlName, 
    /** Class name */
    public readonly string $class, 
    /** Class type */
    public readonly string $classType, 
    /** Class subtype */
    public readonly string $classSubtype, 
    /** View to be used for the step */
    public readonly string $renderViewName, 
    /** View type */
    public readonly string $renderViewType, 
    /** View subtype – where is the view relative to view folder */
    public readonly string $renderViewSubtype, 
    /** Classes / views come from a package */
    public readonly bool $isPackage  ) {}
}