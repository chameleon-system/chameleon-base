<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTplPage;
use ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance;

class CmsTplPageCmsMasterPagedefSpot {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Model */
private string $model = '', 
    // TCMSFieldLookupParentID
/** @var CmsTplPage|null - Layout */
private ?CmsTplPage $cmsTplPage = null
, 
    // TCMSFieldLookupParentID
/** @var CmsTplModuleInstance|null - Module instance */
private ?CmsTplModuleInstance $cmsTplModuleInstance = null
, 
    // TCMSFieldVarchar
/** @var string - Module view */
private string $view = ''  ) {}

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
public function getModel(): string
{
    return $this->model;
}
public function setModel(string $model): self
{
    $this->model = $model;

    return $this;
}


  
    // TCMSFieldLookupParentID
public function getCmsTplPage(): ?CmsTplPage
{
    return $this->cmsTplPage;
}

public function setCmsTplPage(?CmsTplPage $cmsTplPage): self
{
    $this->cmsTplPage = $cmsTplPage;

    return $this;
}


  
    // TCMSFieldLookupParentID
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
public function getView(): string
{
    return $this->view;
}
public function setView(string $view): self
{
    $this->view = $view;

    return $this;
}


  
}
