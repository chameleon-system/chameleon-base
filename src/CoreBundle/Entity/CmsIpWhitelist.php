<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsIpWhitelist {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to cms settings */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsConfig $cmsConfigId, 
    /** IP */
    public readonly string $ip  ) {}
}