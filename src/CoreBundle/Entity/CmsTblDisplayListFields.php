<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTblDisplayListFields {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Field name */
    public readonly string $title, 
    /** Belongs to table */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTblConf $cmsTblConfId, 
    /** Database field name */
    public readonly string $name, 
    /** Database field name of translation */
    public readonly string $cmsTranslationFieldName, 
    /** Field alias (abbreviated) */
    public readonly string $dbAlias, 
    /** Position */
    public readonly int $position, 
    /** Column width */
    public readonly string $width, 
    /** Orientation */
    public readonly string $align, 
    /** Call back function */
    public readonly string $callbackFnc, 
    /** Activate call back functions */
    public readonly bool $useCallback, 
    /** Show in list */
    public readonly bool $showInList, 
    /** Show in sorting */
    public readonly bool $showInSort  ) {}
}