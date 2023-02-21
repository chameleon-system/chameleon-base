<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTplPageCmsMasterPagedefSpot;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\CmsPortal;
use ChameleonSystem\CoreBundle\Entity\CmsTplModule;

class CmsTplModuleInstance {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Instance name */
private string $name = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsTplPageCmsMasterPagedefSpot> - CMS pages dynamic spots */
private Collection $cmsTplPageCmsMasterPagedefSpotCollection = new ArrayCollection()
, 
    // TCMSFieldLookup
/** @var CmsPortal|null - was created in portal */
private ?CmsPortal $cmsPortal = null
, 
    // TCMSFieldVarchar
/** @var string - File name of the module template */
private string $template = '', 
    // TCMSFieldLookup
/** @var CmsTplModule|null - Module ID */
private ?CmsTplModule $cmsTplModule = null
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


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, cmsTplPageCmsMasterPagedefSpot>
*/
public function getCmsTplPageCmsMasterPagedefSpotCollection(): Collection
{
    return $this->cmsTplPageCmsMasterPagedefSpotCollection;
}

public function addCmsTplPageCmsMasterPagedefSpotCollection(cmsTplPageCmsMasterPagedefSpot $cmsTplPageCmsMasterPagedefSpot): self
{
    if (!$this->cmsTplPageCmsMasterPagedefSpotCollection->contains($cmsTplPageCmsMasterPagedefSpot)) {
        $this->cmsTplPageCmsMasterPagedefSpotCollection->add($cmsTplPageCmsMasterPagedefSpot);
        $cmsTplPageCmsMasterPagedefSpot->setCmsTplModuleInstance($this);
    }

    return $this;
}

public function removeCmsTplPageCmsMasterPagedefSpotCollection(cmsTplPageCmsMasterPagedefSpot $cmsTplPageCmsMasterPagedefSpot): self
{
    if ($this->cmsTplPageCmsMasterPagedefSpotCollection->removeElement($cmsTplPageCmsMasterPagedefSpot)) {
        // set the owning side to null (unless already changed)
        if ($cmsTplPageCmsMasterPagedefSpot->getCmsTplModuleInstance() === $this) {
            $cmsTplPageCmsMasterPagedefSpot->setCmsTplModuleInstance(null);
        }
    }

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


  
    // TCMSFieldVarchar
public function getTemplate(): string
{
    return $this->template;
}
public function setTemplate(string $template): self
{
    $this->template = $template;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsTplModule(): ?CmsTplModule
{
    return $this->cmsTplModule;
}

public function setCmsTplModule(?CmsTplModule $cmsTplModule): self
{
    $this->cmsTplModule = $cmsTplModule;

    return $this;
}


  
}
