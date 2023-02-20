<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMenuCustomItem {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Target URL */
private string $url = '', 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsRight[] - Required rights */
private \Doctrine\Common\Collections\Collection $cmsRightMlt = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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


  
    // TCMSFieldVarchar
public function getUrl(): string
{
    return $this->url;
}
public function setUrl(string $url): self
{
    $this->url = $url;

    return $this;
}


  
    // TCMSFieldLookupMultiselectCheckboxes
public function getCmsRightMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsRightMlt;
}
public function setCmsRightMlt(\Doctrine\Common\Collections\Collection $cmsRightMlt): self
{
    $this->cmsRightMlt = $cmsRightMlt;

    return $this;
}


  
}
