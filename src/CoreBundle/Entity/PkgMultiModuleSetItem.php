<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgMultiModuleSetItem {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Module name */
    public readonly string $name, 
    /** Belongs to set */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgMultiModuleSet $pkgMultiModuleSetId, 
    /** Module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Sorting */
    public readonly int $sortOrder, 
    /** System name */
    public readonly string $systemName, 
    /** Alternative link for tabs */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $alternativeTabUrlForAjax  ) {}
}