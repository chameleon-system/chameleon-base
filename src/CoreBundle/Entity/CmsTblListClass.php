<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTblListClass {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Alias name */
    public readonly string $name, 
    /** Belongs to */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTblConf $cmsTblConfId, 
    /** Path to list class */
    public readonly string $classSubtype, 
    /** Class folder */
    public readonly string $classlocation, 
    /** Class name */
    public readonly string $classname  ) {}
}