<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsPortal;
use ChameleonSystem\CoreBundle\Entity\CmsTblConf;
use ChameleonSystem\CoreBundle\Entity\CmsExportProfilesFields;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class CmsExportProfiles {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Profile name */
private string $name = '', 
    // TCMSFieldLookup
/** @var CmsPortal|null - Editorial department */
private ?CmsPortal $cmsPortal = null
, 
    // TCMSFieldLookup
/** @var CmsTblConf|null - Table */
private ?CmsTblConf $cmsTblConf = null
, 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsExportProfilesFields> - Fields to be exported */
private Collection $cmsExportProfilesFieldsCollection = new ArrayCollection()
  ) {}

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


  
    // TCMSFieldLookup
public function getCmsPortal(): ?CmsPortal
{
    return $this->cmsPortal;
}

public function setCmsPortal(?CmsPortal $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsTblConf(): ?CmsTblConf
{
    return $this->cmsTblConf;
}

public function setCmsTblConf(?CmsTblConf $cmsTblConf): self
{
    $this->cmsTblConf = $cmsTblConf;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, cmsExportProfilesFields>
*/
public function getCmsExportProfilesFieldsCollection(): Collection
{
    return $this->cmsExportProfilesFieldsCollection;
}

public function addCmsExportProfilesFieldsCollection(cmsExportProfilesFields $cmsExportProfilesFields): self
{
    if (!$this->cmsExportProfilesFieldsCollection->contains($cmsExportProfilesFields)) {
        $this->cmsExportProfilesFieldsCollection->add($cmsExportProfilesFields);
        $cmsExportProfilesFields->setCmsExportProfiles($this);
    }

    return $this;
}

public function removeCmsExportProfilesFieldsCollection(cmsExportProfilesFields $cmsExportProfilesFields): self
{
    if ($this->cmsExportProfilesFieldsCollection->removeElement($cmsExportProfilesFields)) {
        // set the owning side to null (unless already changed)
        if ($cmsExportProfilesFields->getCmsExportProfiles() === $this) {
            $cmsExportProfilesFields->setCmsExportProfiles(null);
        }
    }

    return $this;
}


  
}
