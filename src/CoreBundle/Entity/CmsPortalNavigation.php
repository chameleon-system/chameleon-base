<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsPortalNavigation {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Belongs to portal */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Belongs to portal */
private ?string $cmsPortalId = null
, 
    // TCMSFieldVarchar
/** @var string - Navigation title */
private string $name = 'neue Navigation', 
    // TCMSFieldNavigationTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Start node in navigation tree */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $treeNode = null  ) {}

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
    // TCMSFieldLookup
public function getCmsPortal(): \ChameleonSystem\CoreBundle\Entity\CmsPortal|null
{
    return $this->cmsPortal;
}
public function setCmsPortal(\ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;
    $this->cmsPortalId = $cmsPortal?->getId();

    return $this;
}
public function getCmsPortalId(): ?string
{
    return $this->cmsPortalId;
}
public function setCmsPortalId(?string $cmsPortalId): self
{
    $this->cmsPortalId = $cmsPortalId;
    // todo - load new id
    //$this->cmsPortalId = $?->getId();

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


  
    // TCMSFieldNavigationTreeNode
public function getTreeNode(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->treeNode;
}
public function setTreeNode(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $treeNode): self
{
    $this->treeNode = $treeNode;

    return $this;
}


  
}
