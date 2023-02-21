<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTplPage;
use ChameleonSystem\CoreBundle\Entity\CmsTree;

class CmsTreeNode {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Table of linked record */
private string $tbl = '', 
    // TCMSFieldLookup
/** @var CmsTplPage|null - ID of linked record */
private ?CmsTplPage $con = null
, 
    // TCMSFieldLookup
/** @var CmsTree|null - Navigation item */
private ?CmsTree $cmsTree = null
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
public function getCon(): ?CmsTplPage
{
    return $this->con;
}

public function setCon(?CmsTplPage $con): self
{
    $this->con = $con;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsTree(): ?CmsTree
{
    return $this->cmsTree;
}

public function setCmsTree(?CmsTree $cmsTree): self
{
    $this->cmsTree = $cmsTree;

    return $this;
}


  
}
