<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsCounter {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** System name */
    public readonly string $systemName, 
    /** Owner */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsConfig|\ChameleonSystem\CoreBundle\Entity\CmsPortal|\ChameleonSystem\CoreBundle\Entity\Shop $owner, 
    /** Value */
    public readonly string $value  ) {}
}