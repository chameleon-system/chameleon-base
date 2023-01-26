<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTplPageCmsMasterPagedefSpot {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Model */
    public readonly string $model, 
    /** Layout */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplPage $cmsTplPageId, 
    /** Belongs to cms page template spot */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpot $cmsMasterPagedefSpotId, 
    /** Module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Module view */
    public readonly string $view  ) {}
}