<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTblConf {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsContentBox|null - View in category window */
private \ChameleonSystem\CoreBundle\Entity\CmsContentBox|null $cmsContentBox = null,
/** @var null|string - View in category window */
private ?string $cmsContentBoxId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblListClass|null - List view default class */
private \ChameleonSystem\CoreBundle\Entity\CmsTblListClass|null $cmsTblListClass = null,
/** @var null|string - List view default class */
private ?string $cmsTblListClassId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplPage|null - Preview page */
private \ChameleonSystem\CoreBundle\Entity\CmsTplPage|null $cmsTplPage = null,
/** @var null|string - Preview page */
private ?string $cmsTplPageId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsUsergroup|null - Table belongs to group */
private \ChameleonSystem\CoreBundle\Entity\CmsUsergroup|null $cmsUsergroup = null,
/** @var null|string - Table belongs to group */
private ?string $cmsUsergroupId = null
, 
    // TCMSFieldVarchar
/** @var string - SQL table name */
private string $name = '', 
    // TCMSFieldOption
/** @var string - Database object type */
private string $dbobjectType = 'Customer', 
    // TCMSFieldVarchar
/** @var string - Title */
private string $translation = '', 
    // TCMSFieldOption
/** @var string - MySql Engine */
private string $engine = 'InnoDB', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblFieldTab[] - Field category/tabs */
private \Doctrine\Common\Collections\Collection $cmsTblFieldTabCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldText
/** @var string - List query */
private string $listQuery = '', 
    // TCMSFieldBoolean
/** @var bool - Table contains only one record */
private bool $onlyOneRecordTbl = false, 
    // TCMSFieldBoolean
/** @var bool - Activate multi language */
private bool $isMultilanguage = false, 
    // TCMSFieldBoolean
/** @var bool - Activate workflow */
private bool $isWorkflow = false, 
    // TCMSFieldBoolean
/** @var bool - Activate locking */
private bool $lockingActive = false, 
    // TCMSFieldBoolean
/** @var bool - Enable changelog */
private bool $changelogActive = false, 
    // TCMSFieldBoolean
/** @var bool - Enable revision management */
private bool $revisionManagementActive = false, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsFieldConf[] - Record fields */
private \Doctrine\Common\Collections\Collection $cmsFieldConfMltCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblDisplayListFields[] - List fields */
private \Doctrine\Common\Collections\Collection $propertyListFieldsCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblDisplayOrderfields[] - Sort fields */
private \Doctrine\Common\Collections\Collection $propertyOrderFieldsCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Name field */
private string $nameColumn = '', 
    // TCMSFieldVarchar
/** @var string - Callback function for the name field */
private string $nameColumnCallback = '', 
    // TCMSFieldVarchar
/** @var string - Display field */
private string $displayColumn = '', 
    // TCMSFieldVarchar
/** @var string - Callback function for the display field */
private string $displayColumnCallback = '', 
    // TCMSFieldVarchar
/** @var string - Group field */
private string $listGroupField = '', 
    // TCMSFieldVarchar
/** @var string - Group field title */
private string $listGroupFieldHeader = '', 
    // TCMSFieldVarchar
/** @var string - Group field column name */
private string $listGroupFieldColumn = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblListClass[] - List views */
private \Doctrine\Common\Collections\Collection $cmsTblListClassCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Table editor php class */
private string $tableEditorClass = '', 
    // TCMSFieldVarchar
/** @var string - Path to table editor class */
private string $tableEditorClassSubtype = '', 
    // TCMSFieldOption
/** @var string - Class type */
private string $tableEditorClassType = 'Core', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblConfRestrictions[] - List restrictions */
private \Doctrine\Common\Collections\Collection $cmsTblConfRestrictionsCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldSmallIconList
/** @var string - Icon */
private string $iconList = '', 
    // TCMSFieldBoolean
/** @var bool - Show preview button in records */
private bool $showPreviewbutton = false, 
    // TCMSFieldBoolean
/** @var bool - Rename on copy */
private bool $renameOnCopy = false, 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] - Rights - Create new record */
private \Doctrine\Common\Collections\Collection $cmsRoleMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] - Rights - Modify record */
private \Doctrine\Common\Collections\Collection $cmsRole1Mlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] - Rights - Delete record */
private \Doctrine\Common\Collections\Collection $cmsRole2Mlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] - Rights - Show all records */
private \Doctrine\Common\Collections\Collection $cmsRole3Mlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] - Rights – Show all records (readonly) */
private \Doctrine\Common\Collections\Collection $cmsRole6Mlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] - Rights - Create new language */
private \Doctrine\Common\Collections\Collection $cmsRole4Mlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] - Rights – Publish record via workflow */
private \Doctrine\Common\Collections\Collection $cmsRole5Mlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] - Right - Revision management */
private \Doctrine\Common\Collections\Collection $cmsRole7Mlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldText
/** @var string - Notes */
private string $notes = '', 
    // TCMSFieldBoolean
/** @var bool - Execute via frontend cache trigger when writing */
private bool $frontendAutoCacheClearEnabled = true, 
    // TCMSFieldVarchar
/** @var string - Is derived from */
private string $dbobjectExtendClass = 'TCMSRecord', 
    // TCMSFieldVarchar
/** @var string - Is extended from: Classtype */
private string $dbobjectExtendSubtype = 'dbobjects', 
    // TCMSFieldOption
/** @var string - Is extended from: Type */
private string $dbobjectExtendType = 'Core', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblExtension[] - Extensions */
private \Doctrine\Common\Collections\Collection $cmsTblExtensionCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldNumber
/** @var int - Automatically limit list object to this number of entries */
private int $autoLimitResults = -1, 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblConfIndex[] - Index definitions */
private \Doctrine\Common\Collections\Collection $cmsTblConfIndexCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Icon Font CSS class */
private string $iconFontCssClass = ''  ) {}

  public function getId(): ?string
  {
    return $this->id;
  }
  public function setId(string $id): self
  {
    $this->id = $id;
    return $this;
  }

  public function getCmsident(): ?int
  {
    return $this->cmsident;
  }
  public function setCmsident(int $cmsident): self
  {
    $this->cmsident = $cmsident;
    return $this;
  }
    // TCMSFieldVarchar
public function getName(): string
{
    return $this->name;
}
public function setName(string $name): self
{
    $this->name = $name;

    return $this;
}


  
    // TCMSFieldOption
public function getDbobjectType(): string
{
    return $this->dbobjectType;
}
public function setDbobjectType(string $dbobjectType): self
{
    $this->dbobjectType = $dbobjectType;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTranslation(): string
{
    return $this->translation;
}
public function setTranslation(string $translation): self
{
    $this->translation = $translation;

    return $this;
}


  
    // TCMSFieldOption
public function getEngine(): string
{
    return $this->engine;
}
public function setEngine(string $engine): self
{
    $this->engine = $engine;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsTblFieldTabCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsTblFieldTabCollection;
}
public function setCmsTblFieldTabCollection(\Doctrine\Common\Collections\Collection $cmsTblFieldTabCollection): self
{
    $this->cmsTblFieldTabCollection = $cmsTblFieldTabCollection;

    return $this;
}


  
    // TCMSFieldText
public function getListQuery(): string
{
    return $this->listQuery;
}
public function setListQuery(string $listQuery): self
{
    $this->listQuery = $listQuery;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsContentBox(): \ChameleonSystem\CoreBundle\Entity\CmsContentBox|null
{
    return $this->cmsContentBox;
}
public function setCmsContentBox(\ChameleonSystem\CoreBundle\Entity\CmsContentBox|null $cmsContentBox): self
{
    $this->cmsContentBox = $cmsContentBox;
    $this->cmsContentBoxId = $cmsContentBox?->getId();

    return $this;
}
public function getCmsContentBoxId(): ?string
{
    return $this->cmsContentBoxId;
}
public function setCmsContentBoxId(?string $cmsContentBoxId): self
{
    $this->cmsContentBoxId = $cmsContentBoxId;
    // todo - load new id
    //$this->cmsContentBoxId = $?->getId();

    return $this;
}



  
    // TCMSFieldBoolean
public function isOnlyOneRecordTbl(): bool
{
    return $this->onlyOneRecordTbl;
}
public function setOnlyOneRecordTbl(bool $onlyOneRecordTbl): self
{
    $this->onlyOneRecordTbl = $onlyOneRecordTbl;

    return $this;
}


  
    // TCMSFieldBoolean
public function isIsMultilanguage(): bool
{
    return $this->isMultilanguage;
}
public function setIsMultilanguage(bool $isMultilanguage): self
{
    $this->isMultilanguage = $isMultilanguage;

    return $this;
}


  
    // TCMSFieldBoolean
public function isIsWorkflow(): bool
{
    return $this->isWorkflow;
}
public function setIsWorkflow(bool $isWorkflow): self
{
    $this->isWorkflow = $isWorkflow;

    return $this;
}


  
    // TCMSFieldBoolean
public function isLockingActive(): bool
{
    return $this->lockingActive;
}
public function setLockingActive(bool $lockingActive): self
{
    $this->lockingActive = $lockingActive;

    return $this;
}


  
    // TCMSFieldBoolean
public function isChangelogActive(): bool
{
    return $this->changelogActive;
}
public function setChangelogActive(bool $changelogActive): self
{
    $this->changelogActive = $changelogActive;

    return $this;
}


  
    // TCMSFieldBoolean
public function isRevisionManagementActive(): bool
{
    return $this->revisionManagementActive;
}
public function setRevisionManagementActive(bool $revisionManagementActive): self
{
    $this->revisionManagementActive = $revisionManagementActive;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsFieldConfMltCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsFieldConfMltCollection;
}
public function setCmsFieldConfMltCollection(\Doctrine\Common\Collections\Collection $cmsFieldConfMltCollection): self
{
    $this->cmsFieldConfMltCollection = $cmsFieldConfMltCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getPropertyListFieldsCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->propertyListFieldsCollection;
}
public function setPropertyListFieldsCollection(\Doctrine\Common\Collections\Collection $propertyListFieldsCollection): self
{
    $this->propertyListFieldsCollection = $propertyListFieldsCollection;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getPropertyOrderFieldsCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->propertyOrderFieldsCollection;
}
public function setPropertyOrderFieldsCollection(\Doctrine\Common\Collections\Collection $propertyOrderFieldsCollection): self
{
    $this->propertyOrderFieldsCollection = $propertyOrderFieldsCollection;

    return $this;
}


  
    // TCMSFieldVarchar
public function getNameColumn(): string
{
    return $this->nameColumn;
}
public function setNameColumn(string $nameColumn): self
{
    $this->nameColumn = $nameColumn;

    return $this;
}


  
    // TCMSFieldVarchar
public function getNameColumnCallback(): string
{
    return $this->nameColumnCallback;
}
public function setNameColumnCallback(string $nameColumnCallback): self
{
    $this->nameColumnCallback = $nameColumnCallback;

    return $this;
}


  
    // TCMSFieldVarchar
public function getDisplayColumn(): string
{
    return $this->displayColumn;
}
public function setDisplayColumn(string $displayColumn): self
{
    $this->displayColumn = $displayColumn;

    return $this;
}


  
    // TCMSFieldVarchar
public function getDisplayColumnCallback(): string
{
    return $this->displayColumnCallback;
}
public function setDisplayColumnCallback(string $displayColumnCallback): self
{
    $this->displayColumnCallback = $displayColumnCallback;

    return $this;
}


  
    // TCMSFieldVarchar
public function getListGroupField(): string
{
    return $this->listGroupField;
}
public function setListGroupField(string $listGroupField): self
{
    $this->listGroupField = $listGroupField;

    return $this;
}


  
    // TCMSFieldVarchar
public function getListGroupFieldHeader(): string
{
    return $this->listGroupFieldHeader;
}
public function setListGroupFieldHeader(string $listGroupFieldHeader): self
{
    $this->listGroupFieldHeader = $listGroupFieldHeader;

    return $this;
}


  
    // TCMSFieldVarchar
public function getListGroupFieldColumn(): string
{
    return $this->listGroupFieldColumn;
}
public function setListGroupFieldColumn(string $listGroupFieldColumn): self
{
    $this->listGroupFieldColumn = $listGroupFieldColumn;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsTblListClassCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsTblListClassCollection;
}
public function setCmsTblListClassCollection(\Doctrine\Common\Collections\Collection $cmsTblListClassCollection): self
{
    $this->cmsTblListClassCollection = $cmsTblListClassCollection;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsTblListClass(): \ChameleonSystem\CoreBundle\Entity\CmsTblListClass|null
{
    return $this->cmsTblListClass;
}
public function setCmsTblListClass(\ChameleonSystem\CoreBundle\Entity\CmsTblListClass|null $cmsTblListClass): self
{
    $this->cmsTblListClass = $cmsTblListClass;
    $this->cmsTblListClassId = $cmsTblListClass?->getId();

    return $this;
}
public function getCmsTblListClassId(): ?string
{
    return $this->cmsTblListClassId;
}
public function setCmsTblListClassId(?string $cmsTblListClassId): self
{
    $this->cmsTblListClassId = $cmsTblListClassId;
    // todo - load new id
    //$this->cmsTblListClassId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getTableEditorClass(): string
{
    return $this->tableEditorClass;
}
public function setTableEditorClass(string $tableEditorClass): self
{
    $this->tableEditorClass = $tableEditorClass;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTableEditorClassSubtype(): string
{
    return $this->tableEditorClassSubtype;
}
public function setTableEditorClassSubtype(string $tableEditorClassSubtype): self
{
    $this->tableEditorClassSubtype = $tableEditorClassSubtype;

    return $this;
}


  
    // TCMSFieldOption
public function getTableEditorClassType(): string
{
    return $this->tableEditorClassType;
}
public function setTableEditorClassType(string $tableEditorClassType): self
{
    $this->tableEditorClassType = $tableEditorClassType;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsTblConfRestrictionsCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsTblConfRestrictionsCollection;
}
public function setCmsTblConfRestrictionsCollection(\Doctrine\Common\Collections\Collection $cmsTblConfRestrictionsCollection): self
{
    $this->cmsTblConfRestrictionsCollection = $cmsTblConfRestrictionsCollection;

    return $this;
}


  
    // TCMSFieldSmallIconList
public function getIconList(): string
{
    return $this->iconList;
}
public function setIconList(string $iconList): self
{
    $this->iconList = $iconList;

    return $this;
}


  
    // TCMSFieldBoolean
public function isShowPreviewbutton(): bool
{
    return $this->showPreviewbutton;
}
public function setShowPreviewbutton(bool $showPreviewbutton): self
{
    $this->showPreviewbutton = $showPreviewbutton;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsTplPage(): \ChameleonSystem\CoreBundle\Entity\CmsTplPage|null
{
    return $this->cmsTplPage;
}
public function setCmsTplPage(\ChameleonSystem\CoreBundle\Entity\CmsTplPage|null $cmsTplPage): self
{
    $this->cmsTplPage = $cmsTplPage;
    $this->cmsTplPageId = $cmsTplPage?->getId();

    return $this;
}
public function getCmsTplPageId(): ?string
{
    return $this->cmsTplPageId;
}
public function setCmsTplPageId(?string $cmsTplPageId): self
{
    $this->cmsTplPageId = $cmsTplPageId;
    // todo - load new id
    //$this->cmsTplPageId = $?->getId();

    return $this;
}



  
    // TCMSFieldBoolean
public function isRenameOnCopy(): bool
{
    return $this->renameOnCopy;
}
public function setRenameOnCopy(bool $renameOnCopy): self
{
    $this->renameOnCopy = $renameOnCopy;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsUsergroup(): \ChameleonSystem\CoreBundle\Entity\CmsUsergroup|null
{
    return $this->cmsUsergroup;
}
public function setCmsUsergroup(\ChameleonSystem\CoreBundle\Entity\CmsUsergroup|null $cmsUsergroup): self
{
    $this->cmsUsergroup = $cmsUsergroup;
    $this->cmsUsergroupId = $cmsUsergroup?->getId();

    return $this;
}
public function getCmsUsergroupId(): ?string
{
    return $this->cmsUsergroupId;
}
public function setCmsUsergroupId(?string $cmsUsergroupId): self
{
    $this->cmsUsergroupId = $cmsUsergroupId;
    // todo - load new id
    //$this->cmsUsergroupId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookupMultiselectCheckboxes
public function getCmsRoleMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsRoleMlt;
}
public function setCmsRoleMlt(\Doctrine\Common\Collections\Collection $cmsRoleMlt): self
{
    $this->cmsRoleMlt = $cmsRoleMlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getCmsRole1Mlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsRole1Mlt;
}
public function setCmsRole1Mlt(\Doctrine\Common\Collections\Collection $cmsRole1Mlt): self
{
    $this->cmsRole1Mlt = $cmsRole1Mlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getCmsRole2Mlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsRole2Mlt;
}
public function setCmsRole2Mlt(\Doctrine\Common\Collections\Collection $cmsRole2Mlt): self
{
    $this->cmsRole2Mlt = $cmsRole2Mlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getCmsRole3Mlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsRole3Mlt;
}
public function setCmsRole3Mlt(\Doctrine\Common\Collections\Collection $cmsRole3Mlt): self
{
    $this->cmsRole3Mlt = $cmsRole3Mlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getCmsRole6Mlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsRole6Mlt;
}
public function setCmsRole6Mlt(\Doctrine\Common\Collections\Collection $cmsRole6Mlt): self
{
    $this->cmsRole6Mlt = $cmsRole6Mlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getCmsRole4Mlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsRole4Mlt;
}
public function setCmsRole4Mlt(\Doctrine\Common\Collections\Collection $cmsRole4Mlt): self
{
    $this->cmsRole4Mlt = $cmsRole4Mlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getCmsRole5Mlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsRole5Mlt;
}
public function setCmsRole5Mlt(\Doctrine\Common\Collections\Collection $cmsRole5Mlt): self
{
    $this->cmsRole5Mlt = $cmsRole5Mlt;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getCmsRole7Mlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsRole7Mlt;
}
public function setCmsRole7Mlt(\Doctrine\Common\Collections\Collection $cmsRole7Mlt): self
{
    $this->cmsRole7Mlt = $cmsRole7Mlt;

    return $this;
}


  
    // TCMSFieldText
public function getNotes(): string
{
    return $this->notes;
}
public function setNotes(string $notes): self
{
    $this->notes = $notes;

    return $this;
}


  
    // TCMSFieldBoolean
public function isFrontendAutoCacheClearEnabled(): bool
{
    return $this->frontendAutoCacheClearEnabled;
}
public function setFrontendAutoCacheClearEnabled(bool $frontendAutoCacheClearEnabled): self
{
    $this->frontendAutoCacheClearEnabled = $frontendAutoCacheClearEnabled;

    return $this;
}


  
    // TCMSFieldVarchar
public function getDbobjectExtendClass(): string
{
    return $this->dbobjectExtendClass;
}
public function setDbobjectExtendClass(string $dbobjectExtendClass): self
{
    $this->dbobjectExtendClass = $dbobjectExtendClass;

    return $this;
}


  
    // TCMSFieldVarchar
public function getDbobjectExtendSubtype(): string
{
    return $this->dbobjectExtendSubtype;
}
public function setDbobjectExtendSubtype(string $dbobjectExtendSubtype): self
{
    $this->dbobjectExtendSubtype = $dbobjectExtendSubtype;

    return $this;
}


  
    // TCMSFieldOption
public function getDbobjectExtendType(): string
{
    return $this->dbobjectExtendType;
}
public function setDbobjectExtendType(string $dbobjectExtendType): self
{
    $this->dbobjectExtendType = $dbobjectExtendType;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsTblExtensionCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsTblExtensionCollection;
}
public function setCmsTblExtensionCollection(\Doctrine\Common\Collections\Collection $cmsTblExtensionCollection): self
{
    $this->cmsTblExtensionCollection = $cmsTblExtensionCollection;

    return $this;
}


  
    // TCMSFieldNumber
public function getAutoLimitResults(): int
{
    return $this->autoLimitResults;
}
public function setAutoLimitResults(int $autoLimitResults): self
{
    $this->autoLimitResults = $autoLimitResults;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsTblConfIndexCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsTblConfIndexCollection;
}
public function setCmsTblConfIndexCollection(\Doctrine\Common\Collections\Collection $cmsTblConfIndexCollection): self
{
    $this->cmsTblConfIndexCollection = $cmsTblConfIndexCollection;

    return $this;
}


  
    // TCMSFieldVarchar
public function getIconFontCssClass(): string
{
    return $this->iconFontCssClass;
}
public function setIconFontCssClass(string $iconFontCssClass): self
{
    $this->iconFontCssClass = $iconFontCssClass;

    return $this;
}


  
}
