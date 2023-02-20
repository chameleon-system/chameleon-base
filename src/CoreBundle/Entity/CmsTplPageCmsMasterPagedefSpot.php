<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTplPageCmsMasterPagedefSpot {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplPage|null - Layout */
private \ChameleonSystem\CoreBundle\Entity\CmsTplPage|null $cmsTplPage = null,
/** @var null|string - Layout */
private ?string $cmsTplPageId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpot|null - Belongs to cms page template spot */
private \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpot|null $cmsMasterPagedefSpot = null,
/** @var null|string - Belongs to cms page template spot */
private ?string $cmsMasterPagedefSpotId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null - Module instance */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance = null,
/** @var null|string - Module instance */
private ?string $cmsTplModuleInstanceId = null
, 
    // TCMSFieldVarchar
/** @var string - Model */
private string $model = '', 
    // TCMSFieldVarchar
/** @var string - Module view */
private string $view = ''  ) {}

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
public function getModel(): string
{
    return $this->model;
}
public function setModel(string $model): self
{
    $this->model = $model;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsTplPage(): \ChameleonSystem\CoreBundle\Entity\CmsTplPage|null
{
    return $this->cmsTplPage;
}
public function setCmsTplPage(\ChameleonSystem\CoreBundle\Entity\CmsTplPage|null $cmsTplPage): self
{
    $this->cmsTplPage = $cmsTplPage;
    $this->cmsTplPageId = $cmsTplPage?->getId();

    return $this;
}
public function getCmsTplPageId(): ?string
{
    return $this->cmsTplPageId;
}
public function setCmsTplPageId(?string $cmsTplPageId): self
{
    $this->cmsTplPageId = $cmsTplPageId;
    // todo - load new id
    //$this->cmsTplPageId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getCmsMasterPagedefSpot(): \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpot|null
{
    return $this->cmsMasterPagedefSpot;
}
public function setCmsMasterPagedefSpot(\ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpot|null $cmsMasterPagedefSpot): self
{
    $this->cmsMasterPagedefSpot = $cmsMasterPagedefSpot;
    $this->cmsMasterPagedefSpotId = $cmsMasterPagedefSpot?->getId();

    return $this;
}
public function getCmsMasterPagedefSpotId(): ?string
{
    return $this->cmsMasterPagedefSpotId;
}
public function setCmsMasterPagedefSpotId(?string $cmsMasterPagedefSpotId): self
{
    $this->cmsMasterPagedefSpotId = $cmsMasterPagedefSpotId;
    // todo - load new id
    //$this->cmsMasterPagedefSpotId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getCmsTplModuleInstance(): \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null
{
    return $this->cmsTplModuleInstance;
}
public function setCmsTplModuleInstance(\ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance): self
{
    $this->cmsTplModuleInstance = $cmsTplModuleInstance;
    $this->cmsTplModuleInstanceId = $cmsTplModuleInstance?->getId();

    return $this;
}
public function getCmsTplModuleInstanceId(): ?string
{
    return $this->cmsTplModuleInstanceId;
}
public function setCmsTplModuleInstanceId(?string $cmsTplModuleInstanceId): self
{
    $this->cmsTplModuleInstanceId = $cmsTplModuleInstanceId;
    // todo - load new id
    //$this->cmsTplModuleInstanceId = $?->getId();

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
