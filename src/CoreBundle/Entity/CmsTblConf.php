<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTblConf {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** SQL table name */
    public readonly string $name, 
    /** Database object type */
    public readonly string $dbobjectType, 
    /** Title */
    public readonly string $translation, 
    /** MySql Engine */
    public readonly string $engine, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsTblFieldTab[] Field category/tabs */
    public readonly array $cmsTblFieldTab, 
    /** List query */
    public readonly string $listQuery, 
    /** View in category window */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsContentBox $cmsContentBoxId, 
    /** Table contains only one record */
    public readonly bool $onlyOneRecordTbl, 
    /** Activate multi language */
    public readonly bool $isMultilanguage, 
    /** Activate workflow */
    public readonly bool $isWorkflow, 
    /** Activate locking */
    public readonly bool $lockingActive, 
    /** Enable changelog */
    public readonly bool $changelogActive, 
    /** Enable revision management */
    public readonly bool $revisionManagementActive, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsFieldConf[] Record fields */
    public readonly array $cmsFieldConfMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsTblDisplayListFields[] List fields */
    public readonly array $propertyListFields, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsTblDisplayOrderfields[] Sort fields */
    public readonly array $propertyOrderFields, 
    /** Name field */
    public readonly string $nameColumn, 
    /** Callback function for the name field */
    public readonly string $nameColumnCallback, 
    /** Display field */
    public readonly string $displayColumn, 
    /** Callback function for the display field */
    public readonly string $displayColumnCallback, 
    /** Group field */
    public readonly string $listGroupField, 
    /** Group field title */
    public readonly string $listGroupFieldHeader, 
    /** Group field column name */
    public readonly string $listGroupFieldColumn, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsTblListClass[] List views */
    public readonly array $cmsTblListClass, 
    /** List view default class */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTblListClass $cmsTblListClassId, 
    /** Table editor php class */
    public readonly string $tableEditorClass, 
    /** Path to table editor class */
    public readonly string $tableEditorClassSubtype, 
    /** Class type */
    public readonly string $tableEditorClassType, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsTblConfRestrictions[] List restrictions */
    public readonly array $cmsTblConfRestrictions, 
    /** Icon */
    public readonly string $iconList, 
    /** Show preview button in records */
    public readonly bool $showPreviewbutton, 
    /** Preview page */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplPage $cmsTplPageId, 
    /** Rename on copy */
    public readonly bool $renameOnCopy, 
    /** Table belongs to group */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsUsergroup $cmsUsergroupId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] Rights - Create new record */
    public readonly array $cmsRoleMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] Rights - Modify record */
    public readonly array $cmsRole1Mlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] Rights - Delete record */
    public readonly array $cmsRole2Mlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] Rights - Show all records */
    public readonly array $cmsRole3Mlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] Rights – Show all records (readonly) */
    public readonly array $cmsRole6Mlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] Rights - Create new language */
    public readonly array $cmsRole4Mlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] Rights – Publish record via workflow */
    public readonly array $cmsRole5Mlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] Right - Revision management */
    public readonly array $cmsRole7Mlt, 
    /** Notes */
    public readonly string $notes, 
    /** Execute via frontend cache trigger when writing */
    public readonly bool $frontendAutoCacheClearEnabled, 
    /** Is derived from */
    public readonly string $dbobjectExtendClass, 
    /** Is extended from: Classtype */
    public readonly string $dbobjectExtendSubtype, 
    /** Is extended from: Type */
    public readonly string $dbobjectExtendType, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsTblExtension[] Extensions */
    public readonly array $cmsTblExtension, 
    /** Automatically limit list object to this number of entries */
    public readonly string $autoLimitResults, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsTblConfIndex[] Index definitions */
    public readonly array $cmsTblConfIndex, 
    /** Icon Font CSS class */
    public readonly string $iconFontCssClass  ) {}
}