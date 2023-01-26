<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsDocumentTree {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Category name */
    public readonly string $name, 
    /** Parent ID */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsDocumentTree $parentId, 
    /** Depth */
    public readonly string $depth, 
    /** Hidden? */
    public readonly bool $hidden, 
    /** Sort sequence */
    public readonly string $entrySort  ) {}
}