<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataExtranetModuleMyAccount {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Headline */
    public readonly string $headline, 
    /** Introduction text */
    public readonly string $intro  ) {}
}