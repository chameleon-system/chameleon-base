<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance;
use ChameleonSystem\CoreBundle\Entity\ModuleCustomlistConfigSortfields;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class ModuleCustomlistConfig {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsTplModuleInstance|null - Belongs to module instance */
private ?CmsTplModuleInstance $cmsTplModuleInstance = null
, 
    // TCMSFieldVarchar
/** @var string - Title */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Items per page */
private string $recordsPerPage = '0', 
    // TCMSFieldVarchar
/** @var string - Grouping field */
private string $groupField = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, ModuleCustomlistConfigSortfields> - Sorting */
private Collection $orderinfoCollection = new ArrayCollection()
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
    // TCMSFieldLookup
public function getCmsTplModuleInstance(): ?CmsTplModuleInstance
{
    return $this->cmsTplModuleInstance;
}

public function setCmsTplModuleInstance(?CmsTplModuleInstance $cmsTplModuleInstance): self
{
    $this->cmsTplModuleInstance = $cmsTplModuleInstance;

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
public function getRecordsPerPage(): string
{
    return $this->recordsPerPage;
}
public function setRecordsPerPage(string $recordsPerPage): self
{
    $this->recordsPerPage = $recordsPerPage;

    return $this;
}


  
    // TCMSFieldVarchar
public function getGroupField(): string
{
    return $this->groupField;
}
public function setGroupField(string $groupField): self
{
    $this->groupField = $groupField;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, ModuleCustomlistConfigSortfields>
*/
public function getOrderinfoCollection(): Collection
{
    return $this->orderinfoCollection;
}

public function addOrderinfoCollection(ModuleCustomlistConfigSortfields $orderinfo): self
{
    if (!$this->orderinfoCollection->contains($orderinfo)) {
        $this->orderinfoCollection->add($orderinfo);
        $orderinfo->setModuleCustomlistConfig($this);
    }

    return $this;
}

public function removeOrderinfoCollection(ModuleCustomlistConfigSortfields $orderinfo): self
{
    if ($this->orderinfoCollection->removeElement($orderinfo)) {
        // set the owning side to null (unless already changed)
        if ($orderinfo->getModuleCustomlistConfig() === $this) {
            $orderinfo->setModuleCustomlistConfig(null);
        }
    }

    return $this;
}


  
}