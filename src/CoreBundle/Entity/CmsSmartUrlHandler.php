<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsSmartUrlHandler {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** PHP class */
    public readonly string $name, 
    /** Path to smart URL handler class */
    public readonly string $classSubtype, 
    /** PHP class type */
    public readonly string $classType, 
    /** Active */
    public readonly bool $active, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] Portal selection */
    public readonly array $cmsPortalMlt, 
    /** Position */
    public readonly int $position  ) {}
}