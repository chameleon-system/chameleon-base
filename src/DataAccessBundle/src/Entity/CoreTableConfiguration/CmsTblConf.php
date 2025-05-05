<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreTableConfiguration;

use ChameleonSystem\DataAccessBundle\Entity\Core\CmsContentBox;
use ChameleonSystem\DataAccessBundle\Entity\Core\CmsRole;
use ChameleonSystem\DataAccessBundle\Entity\Core\CmsUsergroup;
use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTplPage;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CmsTblConf
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

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
        /** @var Collection<int, CmsTblFieldTab> - Field category/tabs */
        private Collection $cmsTblFieldTabCollection = new ArrayCollection(),
        // TCMSFieldText
        /** @var string - List query */
        private string $listQuery = '',
        // TCMSFieldLookup
        /** @var CmsContentBox|null - View in category window */
        private ?CmsContentBox $cmsContentBox = null,
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
        /** @var Collection<int, CmsFieldConf> - Record fields */
        private Collection $cmsFieldConfMltCollection = new ArrayCollection(),
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsTblDisplayListFields> - List fields */
        private Collection $propertyListFieldsCollection = new ArrayCollection(),
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsTblDisplayOrderfields> - Sort fields */
        private Collection $propertyOrderFieldsCollection = new ArrayCollection(),
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
        /** @var Collection<int, CmsTblListClass> - List views */
        private Collection $cmsTblListClassCollection = new ArrayCollection(),
        // TCMSFieldLookupListClass
        /** @var CmsTblListClass|null - List view default class */
        private ?CmsTblListClass $cmsTblListClass = null,
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
        /** @var Collection<int, CmsTblConfRestrictions> - List restrictions */
        private Collection $cmsTblConfRestrictionsCollection = new ArrayCollection(),
        // TCMSFieldBoolean
        /** @var bool - Show preview button in records */
        private bool $showPreviewbutton = false,
        // TCMSFieldExtendedLookup
        /** @var CmsTplPage|null - Preview page */
        private ?CmsTplPage $cmsTplPage = null,
        // TCMSFieldBoolean
        /** @var bool - Rename on copy */
        private bool $renameOnCopy = false,
        // TCMSFieldLookup
        /** @var CmsUsergroup|null - Table belongs to group */
        private ?CmsUsergroup $cmsUsergroup = null,
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsRole> - Rights - Create new record */
        private Collection $cmsRoleCollection = new ArrayCollection(),
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsRole> - Rights - Modify record */
        private Collection $cmsRole1Collection = new ArrayCollection(),
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsRole> - Rights - Delete record */
        private Collection $cmsRole2Collection = new ArrayCollection(),
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsRole> - Rights - Show all records */
        private Collection $cmsRole3Collection = new ArrayCollection(),
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsRole> - Rights – Show all records (readonly) */
        private Collection $cmsRole6Collection = new ArrayCollection(),
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsRole> - Rights - Create new language */
        private Collection $cmsRole4Collection = new ArrayCollection(),
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsRole> - Rights – Publish record via workflow */
        private Collection $cmsRole5Collection = new ArrayCollection(),
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsRole> - Right - Revision management */
        private Collection $cmsRole7Collection = new ArrayCollection(),
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
        /** @var Collection<int, CmsTblExtension> - Extensions */
        private Collection $cmsTblExtensionCollection = new ArrayCollection(),
        // TCMSFieldNumber
        /** @var int - Automatically limit list object to this number of entries */
        private int $autoLimitResults = -1,
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsTblConfIndex> - Index definitions */
        private Collection $cmsTblConfIndexCollection = new ArrayCollection(),
        // TCMSFieldVarchar
        /** @var string - Icon Font CSS class */
        private string $iconFontCssClass = ''
    ) {
    }

    public function getId(): string
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

    /**
     * @return Collection<int, CmsTblFieldTab>
     */
    public function getCmsTblFieldTabCollection(): Collection
    {
        return $this->cmsTblFieldTabCollection;
    }

    public function addCmsTblFieldTabCollection(CmsTblFieldTab $cmsTblFieldTab): self
    {
        if (!$this->cmsTblFieldTabCollection->contains($cmsTblFieldTab)) {
            $this->cmsTblFieldTabCollection->add($cmsTblFieldTab);
            $cmsTblFieldTab->setCmsTblConf($this);
        }

        return $this;
    }

    public function removeCmsTblFieldTabCollection(CmsTblFieldTab $cmsTblFieldTab): self
    {
        if ($this->cmsTblFieldTabCollection->removeElement($cmsTblFieldTab)) {
            // set the owning side to null (unless already changed)
            if ($cmsTblFieldTab->getCmsTblConf() === $this) {
                $cmsTblFieldTab->setCmsTblConf(null);
            }
        }

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
    public function getCmsContentBox(): ?CmsContentBox
    {
        return $this->cmsContentBox;
    }

    public function setCmsContentBox(?CmsContentBox $cmsContentBox): self
    {
        $this->cmsContentBox = $cmsContentBox;

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

    /**
     * @return Collection<int, CmsFieldConf>
     */
    public function getCmsFieldConfMltCollection(): Collection
    {
        return $this->cmsFieldConfMltCollection;
    }

    public function addCmsFieldConfMltCollection(CmsFieldConf $cmsFieldConfMlt): self
    {
        if (!$this->cmsFieldConfMltCollection->contains($cmsFieldConfMlt)) {
            $this->cmsFieldConfMltCollection->add($cmsFieldConfMlt);
            $cmsFieldConfMlt->setCmsTblConf($this);
        }

        return $this;
    }

    public function removeCmsFieldConfMltCollection(CmsFieldConf $cmsFieldConfMlt): self
    {
        if ($this->cmsFieldConfMltCollection->removeElement($cmsFieldConfMlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsFieldConfMlt->getCmsTblConf() === $this) {
                $cmsFieldConfMlt->setCmsTblConf(null);
            }
        }

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsTblDisplayListFields>
     */
    public function getPropertyListFieldsCollection(): Collection
    {
        return $this->propertyListFieldsCollection;
    }

    public function addPropertyListFieldsCollection(CmsTblDisplayListFields $propertyListFields): self
    {
        if (!$this->propertyListFieldsCollection->contains($propertyListFields)) {
            $this->propertyListFieldsCollection->add($propertyListFields);
            $propertyListFields->setCmsTblConf($this);
        }

        return $this;
    }

    public function removePropertyListFieldsCollection(CmsTblDisplayListFields $propertyListFields): self
    {
        if ($this->propertyListFieldsCollection->removeElement($propertyListFields)) {
            // set the owning side to null (unless already changed)
            if ($propertyListFields->getCmsTblConf() === $this) {
                $propertyListFields->setCmsTblConf(null);
            }
        }

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsTblDisplayOrderfields>
     */
    public function getPropertyOrderFieldsCollection(): Collection
    {
        return $this->propertyOrderFieldsCollection;
    }

    public function addPropertyOrderFieldsCollection(CmsTblDisplayOrderfields $propertyOrderFields): self
    {
        if (!$this->propertyOrderFieldsCollection->contains($propertyOrderFields)) {
            $this->propertyOrderFieldsCollection->add($propertyOrderFields);
            $propertyOrderFields->setCmsTblConf($this);
        }

        return $this;
    }

    public function removePropertyOrderFieldsCollection(CmsTblDisplayOrderfields $propertyOrderFields): self
    {
        if ($this->propertyOrderFieldsCollection->removeElement($propertyOrderFields)) {
            // set the owning side to null (unless already changed)
            if ($propertyOrderFields->getCmsTblConf() === $this) {
                $propertyOrderFields->setCmsTblConf(null);
            }
        }

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

    /**
     * @return Collection<int, CmsTblListClass>
     */
    public function getCmsTblListClassCollection(): Collection
    {
        return $this->cmsTblListClassCollection;
    }

    public function addCmsTblListClassCollection(CmsTblListClass $cmsTblListClass): self
    {
        if (!$this->cmsTblListClassCollection->contains($cmsTblListClass)) {
            $this->cmsTblListClassCollection->add($cmsTblListClass);
            $cmsTblListClass->setCmsTblConf($this);
        }

        return $this;
    }

    public function removeCmsTblListClassCollection(CmsTblListClass $cmsTblListClass): self
    {
        if ($this->cmsTblListClassCollection->removeElement($cmsTblListClass)) {
            // set the owning side to null (unless already changed)
            if ($cmsTblListClass->getCmsTblConf() === $this) {
                $cmsTblListClass->setCmsTblConf(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookupListClass
    public function getCmsTblListClass(): ?CmsTblListClass
    {
        return $this->cmsTblListClass;
    }

    public function setCmsTblListClass(?CmsTblListClass $cmsTblListClass): self
    {
        $this->cmsTblListClass = $cmsTblListClass;

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

    /**
     * @return Collection<int, CmsTblConfRestrictions>
     */
    public function getCmsTblConfRestrictionsCollection(): Collection
    {
        return $this->cmsTblConfRestrictionsCollection;
    }

    public function addCmsTblConfRestrictionsCollection(CmsTblConfRestrictions $cmsTblConfRestrictions): self
    {
        if (!$this->cmsTblConfRestrictionsCollection->contains($cmsTblConfRestrictions)) {
            $this->cmsTblConfRestrictionsCollection->add($cmsTblConfRestrictions);
            $cmsTblConfRestrictions->setCmsTblConf($this);
        }

        return $this;
    }

    public function removeCmsTblConfRestrictionsCollection(CmsTblConfRestrictions $cmsTblConfRestrictions): self
    {
        if ($this->cmsTblConfRestrictionsCollection->removeElement($cmsTblConfRestrictions)) {
            // set the owning side to null (unless already changed)
            if ($cmsTblConfRestrictions->getCmsTblConf() === $this) {
                $cmsTblConfRestrictions->setCmsTblConf(null);
            }
        }

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

    // TCMSFieldExtendedLookup
    public function getCmsTplPage(): ?CmsTplPage
    {
        return $this->cmsTplPage;
    }

    public function setCmsTplPage(?CmsTplPage $cmsTplPage): self
    {
        $this->cmsTplPage = $cmsTplPage;

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
    public function getCmsUsergroup(): ?CmsUsergroup
    {
        return $this->cmsUsergroup;
    }

    public function setCmsUsergroup(?CmsUsergroup $cmsUsergroup): self
    {
        $this->cmsUsergroup = $cmsUsergroup;

        return $this;
    }

    // TCMSFieldLookupMultiselectCheckboxes

    /**
     * @return Collection<int, CmsRole>
     */
    public function getCmsRoleCollection(): Collection
    {
        return $this->cmsRoleCollection;
    }

    public function addCmsRoleCollection(CmsRole $cmsRoleMlt): self
    {
        if (!$this->cmsRoleCollection->contains($cmsRoleMlt)) {
            $this->cmsRoleCollection->add($cmsRoleMlt);
            $cmsRoleMlt->set($this);
        }

        return $this;
    }

    public function removeCmsRoleCollection(CmsRole $cmsRoleMlt): self
    {
        if ($this->cmsRoleCollection->removeElement($cmsRoleMlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsRoleMlt->get() === $this) {
                $cmsRoleMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookupMultiselectCheckboxes

    /**
     * @return Collection<int, CmsRole>
     */
    public function getCmsRole1Collection(): Collection
    {
        return $this->cmsRole1Collection;
    }

    public function addCmsRole1Collection(CmsRole $cmsRole1Mlt): self
    {
        if (!$this->cmsRole1Collection->contains($cmsRole1Mlt)) {
            $this->cmsRole1Collection->add($cmsRole1Mlt);
            $cmsRole1Mlt->set($this);
        }

        return $this;
    }

    public function removeCmsRole1Collection(CmsRole $cmsRole1Mlt): self
    {
        if ($this->cmsRole1Collection->removeElement($cmsRole1Mlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsRole1Mlt->get() === $this) {
                $cmsRole1Mlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookupMultiselectCheckboxes

    /**
     * @return Collection<int, CmsRole>
     */
    public function getCmsRole2Collection(): Collection
    {
        return $this->cmsRole2Collection;
    }

    public function addCmsRole2Collection(CmsRole $cmsRole2Mlt): self
    {
        if (!$this->cmsRole2Collection->contains($cmsRole2Mlt)) {
            $this->cmsRole2Collection->add($cmsRole2Mlt);
            $cmsRole2Mlt->set($this);
        }

        return $this;
    }

    public function removeCmsRole2Collection(CmsRole $cmsRole2Mlt): self
    {
        if ($this->cmsRole2Collection->removeElement($cmsRole2Mlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsRole2Mlt->get() === $this) {
                $cmsRole2Mlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookupMultiselectCheckboxes

    /**
     * @return Collection<int, CmsRole>
     */
    public function getCmsRole3Collection(): Collection
    {
        return $this->cmsRole3Collection;
    }

    public function addCmsRole3Collection(CmsRole $cmsRole3Mlt): self
    {
        if (!$this->cmsRole3Collection->contains($cmsRole3Mlt)) {
            $this->cmsRole3Collection->add($cmsRole3Mlt);
            $cmsRole3Mlt->set($this);
        }

        return $this;
    }

    public function removeCmsRole3Collection(CmsRole $cmsRole3Mlt): self
    {
        if ($this->cmsRole3Collection->removeElement($cmsRole3Mlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsRole3Mlt->get() === $this) {
                $cmsRole3Mlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookupMultiselectCheckboxes

    /**
     * @return Collection<int, CmsRole>
     */
    public function getCmsRole6Collection(): Collection
    {
        return $this->cmsRole6Collection;
    }

    public function addCmsRole6Collection(CmsRole $cmsRole6Mlt): self
    {
        if (!$this->cmsRole6Collection->contains($cmsRole6Mlt)) {
            $this->cmsRole6Collection->add($cmsRole6Mlt);
            $cmsRole6Mlt->set($this);
        }

        return $this;
    }

    public function removeCmsRole6Collection(CmsRole $cmsRole6Mlt): self
    {
        if ($this->cmsRole6Collection->removeElement($cmsRole6Mlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsRole6Mlt->get() === $this) {
                $cmsRole6Mlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookupMultiselectCheckboxes

    /**
     * @return Collection<int, CmsRole>
     */
    public function getCmsRole4Collection(): Collection
    {
        return $this->cmsRole4Collection;
    }

    public function addCmsRole4Collection(CmsRole $cmsRole4Mlt): self
    {
        if (!$this->cmsRole4Collection->contains($cmsRole4Mlt)) {
            $this->cmsRole4Collection->add($cmsRole4Mlt);
            $cmsRole4Mlt->set($this);
        }

        return $this;
    }

    public function removeCmsRole4Collection(CmsRole $cmsRole4Mlt): self
    {
        if ($this->cmsRole4Collection->removeElement($cmsRole4Mlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsRole4Mlt->get() === $this) {
                $cmsRole4Mlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookupMultiselectCheckboxes

    /**
     * @return Collection<int, CmsRole>
     */
    public function getCmsRole5Collection(): Collection
    {
        return $this->cmsRole5Collection;
    }

    public function addCmsRole5Collection(CmsRole $cmsRole5Mlt): self
    {
        if (!$this->cmsRole5Collection->contains($cmsRole5Mlt)) {
            $this->cmsRole5Collection->add($cmsRole5Mlt);
            $cmsRole5Mlt->set($this);
        }

        return $this;
    }

    public function removeCmsRole5Collection(CmsRole $cmsRole5Mlt): self
    {
        if ($this->cmsRole5Collection->removeElement($cmsRole5Mlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsRole5Mlt->get() === $this) {
                $cmsRole5Mlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookupMultiselectCheckboxes

    /**
     * @return Collection<int, CmsRole>
     */
    public function getCmsRole7Collection(): Collection
    {
        return $this->cmsRole7Collection;
    }

    public function addCmsRole7Collection(CmsRole $cmsRole7Mlt): self
    {
        if (!$this->cmsRole7Collection->contains($cmsRole7Mlt)) {
            $this->cmsRole7Collection->add($cmsRole7Mlt);
            $cmsRole7Mlt->set($this);
        }

        return $this;
    }

    public function removeCmsRole7Collection(CmsRole $cmsRole7Mlt): self
    {
        if ($this->cmsRole7Collection->removeElement($cmsRole7Mlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsRole7Mlt->get() === $this) {
                $cmsRole7Mlt->set(null);
            }
        }

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

    /**
     * @return Collection<int, CmsTblExtension>
     */
    public function getCmsTblExtensionCollection(): Collection
    {
        return $this->cmsTblExtensionCollection;
    }

    public function addCmsTblExtensionCollection(CmsTblExtension $cmsTblExtension): self
    {
        if (!$this->cmsTblExtensionCollection->contains($cmsTblExtension)) {
            $this->cmsTblExtensionCollection->add($cmsTblExtension);
            $cmsTblExtension->setCmsTblConf($this);
        }

        return $this;
    }

    public function removeCmsTblExtensionCollection(CmsTblExtension $cmsTblExtension): self
    {
        if ($this->cmsTblExtensionCollection->removeElement($cmsTblExtension)) {
            // set the owning side to null (unless already changed)
            if ($cmsTblExtension->getCmsTblConf() === $this) {
                $cmsTblExtension->setCmsTblConf(null);
            }
        }

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

    /**
     * @return Collection<int, CmsTblConfIndex>
     */
    public function getCmsTblConfIndexCollection(): Collection
    {
        return $this->cmsTblConfIndexCollection;
    }

    public function addCmsTblConfIndexCollection(CmsTblConfIndex $cmsTblConfIndex): self
    {
        if (!$this->cmsTblConfIndexCollection->contains($cmsTblConfIndex)) {
            $this->cmsTblConfIndexCollection->add($cmsTblConfIndex);
            $cmsTblConfIndex->setCmsTblConf($this);
        }

        return $this;
    }

    public function removeCmsTblConfIndexCollection(CmsTblConfIndex $cmsTblConfIndex): self
    {
        if ($this->cmsTblConfIndexCollection->removeElement($cmsTblConfIndex)) {
            // set the owning side to null (unless already changed)
            if ($cmsTblConfIndex->getCmsTblConf() === $this) {
                $cmsTblConfIndex->setCmsTblConf(null);
            }
        }

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
