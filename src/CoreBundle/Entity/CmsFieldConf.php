<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsFieldConf {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to Table */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTblConf $cmsTblConfId, 
    /** Field name */
    public readonly string $name, 
    /** Translation */
    public readonly string $translation, 
    /** Field type */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsFieldType $cmsFieldTypeId, 
    /** Belongs to field-category / tab */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTblFieldTab $cmsTblFieldTab, 
    /** Mandatory field */
    public readonly bool $isrequired, 
    /** PHP class */
    public readonly string $fieldclass, 
    /** Field extension subtype */
    public readonly string $fieldclassSubtype, 
    /** PHP class type */
    public readonly string $classType, 
    /** Display mode */
    public readonly string $modifier, 
    /** Default value */
    public readonly string $fieldDefaultValue, 
    /** Field length, value list */
    public readonly string $lengthSet, 
    /** Field type configuration */
    public readonly string $fieldtypeConfig, 
    /** Restrict field access */
    public readonly bool $restrictToGroups, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsUsergroup[] Allowed user groups */
    public readonly array $cmsUsergroupMlt, 
    /** Input field width */
    public readonly string $fieldWidth, 
    /** Position */
    public readonly int $position, 
    /** Help text */
    public readonly string $049Helptext, 
    /** Line color */
    public readonly string $rowHexcolor, 
    /** Multilanguage field (relevant when field-based translations are active) */
    public readonly bool $isTranslatable, 
    /** Regular expression to validate the field */
    public readonly string $validationRegex  ) {}
}