<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTreeNode {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplPage|null - ID of linked record */
private \ChameleonSystem\CoreBundle\Entity\CmsTplPage|null $contid = null,
/** @var null|string - ID of linked record */
private ?string $contidId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree|null - Navigation item */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $cmsTree = null,
/** @var null|string - Navigation item */
private ?string $cmsTreeId = null
, 
    // TCMSFieldBoolean
/** @var bool - Create link */
private bool $active = false, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Activate connection from */
private \DateTime|null $startDate = null, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Deactivate connection after */
private \DateTime|null $endDate = null, 
    // TCMSFieldVarchar
/** @var string - Table of linked record */
private string $tbl = ''  ) {}

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
    // TCMSFieldBoolean
public function isActive(): bool
{
    return $this->active;
}
public function setActive(bool $active): self
{
    $this->active = $active;

    return $this;
}


  
    // TCMSFieldDateTime
public function getStartDate(): \DateTime|null
{
    return $this->startDate;
}
public function setStartDate(\DateTime|null $startDate): self
{
    $this->startDate = $startDate;

    return $this;
}


  
    // TCMSFieldDateTime
public function getEndDate(): \DateTime|null
{
    return $this->endDate;
}
public function setEndDate(\DateTime|null $endDate): self
{
    $this->endDate = $endDate;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTbl(): string
{
    return $this->tbl;
}
public function setTbl(string $tbl): self
{
    $this->tbl = $tbl;

    return $this;
}


  
    // TCMSFieldLookup
public function getContid(): \ChameleonSystem\CoreBundle\Entity\CmsTplPage|null
{
    return $this->contid;
}
public function setContid(\ChameleonSystem\CoreBundle\Entity\CmsTplPage|null $contid): self
{
    $this->contid = $contid;
    $this->contidId = $contid?->getId();

    return $this;
}
public function getContidId(): ?string
{
    return $this->contidId;
}
public function setContidId(?string $contidId): self
{
    $this->contidId = $contidId;
    // todo - load new id
    //$this->contidId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getCmsTree(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->cmsTree;
}
public function setCmsTree(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $cmsTree): self
{
    $this->cmsTree = $cmsTree;
    $this->cmsTreeId = $cmsTree?->getId();

    return $this;
}
public function getCmsTreeId(): ?string
{
    return $this->cmsTreeId;
}
public function setCmsTreeId(?string $cmsTreeId): self
{
    $this->cmsTreeId = $cmsTreeId;
    // todo - load new id
    //$this->cmsTreeId = $?->getId();

    return $this;
}



  
}
