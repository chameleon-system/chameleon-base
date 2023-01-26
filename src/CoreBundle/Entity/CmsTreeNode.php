<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTreeNode {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Create link */
    public readonly bool $active, 
    /** Activate connection from */
    public readonly \DateTime $startDate, 
    /** Deactivate connection after */
    public readonly \DateTime $endDate, 
    /** Table of linked record */
    public readonly string $tbl, 
    /** ID of linked record */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplPage $contid, 
    /** Navigation item */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $cmsTreeId  ) {}
}