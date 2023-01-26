<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsInterfaceManager {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** System name */
    public readonly string $systemname, 
    /** Used class */
    public readonly string $class, 
    /** Class type */
    public readonly string $classType, 
    /** Class subtype */
    public readonly string $classSubtype, 
    /** Description */
    public readonly string $description, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsInterfaceManagerParameter[] Parameter */
    public readonly array $cmsInterfaceManagerParameter, 
    /** Restrict to user groups */
    public readonly bool $restrictToUserGroups, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsUsergroup[] Available for the following groups */
    public readonly array $cmsUsergroupMlt  ) {}
}