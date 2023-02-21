<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTblFieldTab;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\CmsContentBox;
use ChameleonSystem\CoreBundle\Entity\CmsFieldConf;
use ChameleonSystem\CoreBundle\Entity\CmsTblDisplayListFields;
use ChameleonSystem\CoreBundle\Entity\CmsTblDisplayOrderfields;
use ChameleonSystem\CoreBundle\Entity\CmsTblListClass;
use ChameleonSystem\CoreBundle\Entity\CmsTblConfRestrictions;
use ChameleonSystem\CoreBundle\Entity\CmsTplPage;
use ChameleonSystem\CoreBundle\Entity\CmsUsergroup;
use ChameleonSystem\CoreBundle\Entity\CmsTblExtension;
use ChameleonSystem\CoreBundle\Entity\CmsTblConfIndex;

class CmsTblConf {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - SQL table name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Title */
private string $translation = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsTblFieldTab> - Field category/tabs */
private Collection $cmsTblFieldTabCollection = new ArrayCollection()
, 
    // TCMSFieldLookup
/** @var CmsContentBox|null - View in category window */
private ?CmsContentBox $cmsContentBox = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsFieldConf> - Record fields */
private Collection $cmsFieldConfMltCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsTblDisplayListFields> - List fields */
private Collection $propertyListFieldsCollection = new ArrayCollection()
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsTblDisplayOrderfields> - Sort fields */
private Collection $propertyOrderFieldsCollection = new ArrayCollection()
, 
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
/** @var Collection<int, cmsTblListClass> - List views */
private Collection $cmsTblListClassCollection = new ArrayCollection()
, 
    // TCMSFieldLookup
/** @var CmsTblListClass|null - List view default class */
private ?CmsTblListClass $cmsTblListClass = null
, 
    // TCMSFieldVarchar
/** @var string - Table editor php class */
private string $tableEditorClass = '', 
    // TCMSFieldVarchar
/** @var string - Path to table editor class */
private string $tableEditorClassSubtype = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsTblConfRestrictions> - List restrictions */
private Collection $cmsTblConfRestrictionsCollection = new ArrayCollection()
, 
    // TCMSFieldLookup
/** @var CmsTplPage|null - Preview page */
private ?CmsTplPage $cmsTplPage = null
, 
    // TCMSFieldLookup
/** @var CmsUsergroup|null - Table belongs to group */
private ?CmsUsergroup $cmsUsergroup = null
, 
    // TCMSFieldVarchar
/** @var string - Is derived from */
private string $dbobjectExtendClass = 'TCMSRecord', 
    // TCMSFieldVarchar
/** @var string - Is extended from: Classtype */
private string $dbobjectExtendSubtype = 'dbobjects', 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsTblExtension> - Extensions */
private Collection $cmsTblExtensionCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Automatically limit list object to this number of entries */
private string $autoLimitResults = '-1', 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsTblConfIndex> - Index definitions */
private Collection $cmsTblConfIndexCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Icon Font CSS class */
private string $iconFontCssClass = ''  ) {}

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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, cmsTblFieldTab>
*/
public function getCmsTblFieldTabCollection(): Collection
{
    return $this->cmsTblFieldTabCollection;
}

public function addCmsTblFieldTabCollection(cmsTblFieldTab $cmsTblFieldTab): self
{
    if (!$this->cmsTblFieldTabCollection->contains($cmsTblFieldTab)) {
        $this->cmsTblFieldTabCollection->add($cmsTblFieldTab);
        $cmsTblFieldTab->setCmsTblConf($this);
    }

    return $this;
}

public function removeCmsTblFieldTabCollection(cmsTblFieldTab $cmsTblFieldTab): self
{
    if ($this->cmsTblFieldTabCollection->removeElement($cmsTblFieldTab)) {
        // set the owning side to null (unless already changed)
        if ($cmsTblFieldTab->getCmsTblConf() === $this) {
            $cmsTblFieldTab->setCmsTblConf(null);
        }
    }

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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, cmsFieldConf>
*/
public function getCmsFieldConfMltCollection(): Collection
{
    return $this->cmsFieldConfMltCollection;
}

public function addCmsFieldConfMltCollection(cmsFieldConf $cmsFieldConfMlt): self
{
    if (!$this->cmsFieldConfMltCollection->contains($cmsFieldConfMlt)) {
        $this->cmsFieldConfMltCollection->add($cmsFieldConfMlt);
        $cmsFieldConfMlt->setCmsTblConf($this);
    }

    return $this;
}

public function removeCmsFieldConfMltCollection(cmsFieldConf $cmsFieldConfMlt): self
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
* @return Collection<int, cmsTblDisplayListFields>
*/
public function getPropertyListFieldsCollection(): Collection
{
    return $this->propertyListFieldsCollection;
}

public function addPropertyListFieldsCollection(cmsTblDisplayListFields $propertyListFields): self
{
    if (!$this->propertyListFieldsCollection->contains($propertyListFields)) {
        $this->propertyListFieldsCollection->add($propertyListFields);
        $propertyListFields->setCmsTblConf($this);
    }

    return $this;
}

public function removePropertyListFieldsCollection(cmsTblDisplayListFields $propertyListFields): self
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
* @return Collection<int, cmsTblDisplayOrderfields>
*/
public function getPropertyOrderFieldsCollection(): Collection
{
    return $this->propertyOrderFieldsCollection;
}

public function addPropertyOrderFieldsCollection(cmsTblDisplayOrderfields $propertyOrderFields): self
{
    if (!$this->propertyOrderFieldsCollection->contains($propertyOrderFields)) {
        $this->propertyOrderFieldsCollection->add($propertyOrderFields);
        $propertyOrderFields->setCmsTblConf($this);
    }

    return $this;
}

public function removePropertyOrderFieldsCollection(cmsTblDisplayOrderfields $propertyOrderFields): self
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
* @return Collection<int, cmsTblListClass>
*/
public function getCmsTblListClassCollection(): Collection
{
    return $this->cmsTblListClassCollection;
}

public function addCmsTblListClassCollection(cmsTblListClass $cmsTblListClass): self
{
    if (!$this->cmsTblListClassCollection->contains($cmsTblListClass)) {
        $this->cmsTblListClassCollection->add($cmsTblListClass);
        $cmsTblListClass->setCmsTblConf($this);
    }

    return $this;
}

public function removeCmsTblListClassCollection(cmsTblListClass $cmsTblListClass): self
{
    if ($this->cmsTblListClassCollection->removeElement($cmsTblListClass)) {
        // set the owning side to null (unless already changed)
        if ($cmsTblListClass->getCmsTblConf() === $this) {
            $cmsTblListClass->setCmsTblConf(null);
        }
    }

    return $this;
}


  
    // TCMSFieldLookup
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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, cmsTblConfRestrictions>
*/
public function getCmsTblConfRestrictionsCollection(): Collection
{
    return $this->cmsTblConfRestrictionsCollection;
}

public function addCmsTblConfRestrictionsCollection(cmsTblConfRestrictions $cmsTblConfRestrictions): self
{
    if (!$this->cmsTblConfRestrictionsCollection->contains($cmsTblConfRestrictions)) {
        $this->cmsTblConfRestrictionsCollection->add($cmsTblConfRestrictions);
        $cmsTblConfRestrictions->setCmsTblConf($this);
    }

    return $this;
}

public function removeCmsTblConfRestrictionsCollection(cmsTblConfRestrictions $cmsTblConfRestrictions): self
{
    if ($this->cmsTblConfRestrictionsCollection->removeElement($cmsTblConfRestrictions)) {
        // set the owning side to null (unless already changed)
        if ($cmsTblConfRestrictions->getCmsTblConf() === $this) {
            $cmsTblConfRestrictions->setCmsTblConf(null);
        }
    }

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsTplPage(): ?CmsTplPage
{
    return $this->cmsTplPage;
}

public function setCmsTplPage(?CmsTplPage $cmsTplPage): self
{
    $this->cmsTplPage = $cmsTplPage;

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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, cmsTblExtension>
*/
public function getCmsTblExtensionCollection(): Collection
{
    return $this->cmsTblExtensionCollection;
}

public function addCmsTblExtensionCollection(cmsTblExtension $cmsTblExtension): self
{
    if (!$this->cmsTblExtensionCollection->contains($cmsTblExtension)) {
        $this->cmsTblExtensionCollection->add($cmsTblExtension);
        $cmsTblExtension->setCmsTblConf($this);
    }

    return $this;
}

public function removeCmsTblExtensionCollection(cmsTblExtension $cmsTblExtension): self
{
    if ($this->cmsTblExtensionCollection->removeElement($cmsTblExtension)) {
        // set the owning side to null (unless already changed)
        if ($cmsTblExtension->getCmsTblConf() === $this) {
            $cmsTblExtension->setCmsTblConf(null);
        }
    }

    return $this;
}


  
    // TCMSFieldVarchar
public function getAutoLimitResults(): string
{
    return $this->autoLimitResults;
}
public function setAutoLimitResults(string $autoLimitResults): self
{
    $this->autoLimitResults = $autoLimitResults;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, cmsTblConfIndex>
*/
public function getCmsTblConfIndexCollection(): Collection
{
    return $this->cmsTblConfIndexCollection;
}

public function addCmsTblConfIndexCollection(cmsTblConfIndex $cmsTblConfIndex): self
{
    if (!$this->cmsTblConfIndexCollection->contains($cmsTblConfIndex)) {
        $this->cmsTblConfIndexCollection->add($cmsTblConfIndex);
        $cmsTblConfIndex->setCmsTblConf($this);
    }

    return $this;
}

public function removeCmsTblConfIndexCollection(cmsTblConfIndex $cmsTblConfIndex): self
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
