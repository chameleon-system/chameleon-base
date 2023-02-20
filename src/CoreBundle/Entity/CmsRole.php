<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsRole {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] - Is subordinate role of */
private \Doctrine\Common\Collections\Collection $cmsRoleMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldBoolean
/** @var bool - Is selectable */
private bool $isChooseable = true, 
    // TCMSFieldVarchar
/** @var string - CMS role abbreviation */
private string $name = '', 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\CmsRight[] - CMS user rights */
private \Doctrine\Common\Collections\Collection $cmsRightMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldBoolean
/** @var bool - Required by the system */
private bool $isSystem = false, 
    // TCMSFieldVarchar
/** @var string - German translation */
private string $trans = ''  ) {}

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


  
    // TCMSFieldBoolean
public function isIsChooseable(): bool
{
    return $this->isChooseable;
}
public function setIsChooseable(bool $isChooseable): self
{
    $this->isChooseable = $isChooseable;

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


  
    // TCMSFieldBoolean
public function isIsSystem(): bool
{
    return $this->isSystem;
}
public function setIsSystem(bool $isSystem): self
{
    $this->isSystem = $isSystem;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTrans(): string
{
    return $this->trans;
}
public function setTrans(string $trans): self
{
    $this->trans = $trans;

    return $this;
}


  
}
