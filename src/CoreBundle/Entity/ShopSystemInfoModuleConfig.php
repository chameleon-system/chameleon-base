<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSystemInfoModuleConfig {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Optional title */
    public readonly string $name, 
    /** Optional introduction text */
    public readonly string $intro, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopSystemInfo[] Shop info pages to be displayed */
    public readonly array $shopSystemInfoMlt  ) {}
}