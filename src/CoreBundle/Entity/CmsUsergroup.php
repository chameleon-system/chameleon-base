<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsUsergroup {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** German translation */
    public readonly string $name, 
    /** CMS group ID */
    public readonly string $internalIdentifier, 
    /** Is selectable */
    public readonly bool $isChooseable, 
    /** Required by the system */
    public readonly bool $isSystem  ) {}
}