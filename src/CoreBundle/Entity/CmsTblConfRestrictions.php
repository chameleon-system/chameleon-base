<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTblConfRestrictions {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Table */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTblConf $cmsTblConfId, 
    /** Field name */
    public readonly string $name, 
    /** Callback function */
    public readonly string $function  ) {}
}