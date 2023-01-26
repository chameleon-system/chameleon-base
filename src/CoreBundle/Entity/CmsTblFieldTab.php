<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTblFieldTab {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to table */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTblConf $cmsTblConfId, 
    /** Name */
    public readonly string $name, 
    /** Position */
    public readonly int $position, 
    /** System name */
    public readonly string $systemname, 
    /** Description */
    public readonly string $description  ) {}
}