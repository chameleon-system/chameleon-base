<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTblConfIndex {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to table */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTblConf $cmsTblConfId, 
    /** Name */
    public readonly string $name, 
    /** Field list */
    public readonly string $definition, 
    /** Index type */
    public readonly string $type  ) {}
}