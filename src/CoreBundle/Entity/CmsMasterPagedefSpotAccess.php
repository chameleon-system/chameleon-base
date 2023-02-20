<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMasterPagedefSpotAccess {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpot|null - Belongs to cms page template spot */
private \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpot|null $cmsMasterPagedefSpot = null,
/** @var null|string - Belongs to cms page template spot */
private ?string $cmsMasterPagedefSpotId = null
, 
    // TCMSFieldVarchar
/** @var string - Module */
private string $model = '', 
    // TCMSFieldText
/** @var string - Views */
private string $views = ''  ) {}

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


  
    // TCMSFieldText
public function getViews(): string
{
    return $this->views;
}
public function setViews(string $views): self
{
    $this->views = $views;

    return $this;
}


  
}
