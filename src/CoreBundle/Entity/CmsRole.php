<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsRole {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] Is subordinate role of */
    public readonly array $cmsRoleMlt, 
    /** Is selectable */
    public readonly bool $isChooseable, 
    /** CMS role abbreviation */
    public readonly string $name, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsRight[] CMS user rights */
    public readonly array $cmsRightMlt, 
    /** Required by the system */
    public readonly bool $isSystem, 
    /** German translation */
    public readonly string $049Trans  ) {}
}