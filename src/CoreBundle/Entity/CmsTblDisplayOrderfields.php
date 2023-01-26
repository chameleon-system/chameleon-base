<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTblDisplayOrderfields {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Database field name */
    public readonly string $name, 
    /** Order direction */
    public readonly string $sortOrderDirection, 
    /** Position */
    public readonly int $position, 
    /** Belongs to table */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTblConf $cmsTblConfId  ) {}
}