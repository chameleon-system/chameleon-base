<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ModuleText {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Headline */
    public readonly string $name, 
    /** Sub headline */
    public readonly string $subheadline, 
    /** Optional icon */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $icon, 
    /** Content */
    public readonly string $content, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsDocument[] Download files */
    public readonly array $dataPool  ) {}
}