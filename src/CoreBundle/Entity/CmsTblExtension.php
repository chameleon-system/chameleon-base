<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTblExtension {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Text template */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTblConf $cmsTblConfId, 
    /** Classname */
    public readonly string $name, 
    /** List class extension */
    public readonly string $nameList, 
    /** Subtype */
    public readonly string $subtype, 
    /** Type */
    public readonly string $type, 
    /** Position */
    public readonly int $position, 
    /** Name of the last extension before Tadb* */
    public readonly string $virtualItemClassName, 
    /** Name of the last extension before Tadb*List */
    public readonly string $virtualItemClassListName  ) {}
}