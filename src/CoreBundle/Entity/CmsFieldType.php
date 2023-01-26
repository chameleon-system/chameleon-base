<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsFieldType {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $049Trans, 
    /** Auto increment */
    public readonly bool $forceAutoIncrement, 
    /** PHP class subtype */
    public readonly string $classSubtype, 
    /** Field code name */
    public readonly string $constname, 
    /** MySQL data type */
    public readonly string $mysqlType, 
    /** MySQL field length or value list (ENUM) */
    public readonly string $lengthSet, 
    /** Base type */
    public readonly string $baseType, 
    /** Help text */
    public readonly string $helpText, 
    /** Default value */
    public readonly string $mysqlStandardValue, 
    /** PHP class */
    public readonly string $fieldclass, 
    /** PHP class type */
    public readonly string $classType, 
    /** Field contains images */
    public readonly bool $containsImages, 
    /** Field index */
    public readonly string $indextype  ) {}
}