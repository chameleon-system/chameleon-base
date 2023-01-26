<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ModuleListConfig {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Teaser target page */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $targetPage, 
    /** Sort list by */
    public readonly string $moduleListCmsfieldname, 
    /** Order direction */
    public readonly string $sortOrderDirection, 
    /** Belongs to module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Title */
    public readonly string $name, 
    /** Theme */
    public readonly string $subHeadline, 
    /** Text */
    public readonly string $description  ) {}
}