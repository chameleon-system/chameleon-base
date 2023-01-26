<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgNewsletterModuleSignupTeaser {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Login takes place via the following instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\ConfigForSignupModuleInstance $configForSignupModuleInstanceId, 
    /** Heading */
    public readonly string $name, 
    /** Introduction */
    public readonly string $intro  ) {}
}