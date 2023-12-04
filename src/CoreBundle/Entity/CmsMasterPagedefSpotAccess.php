<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsMasterPagedefSpot;

class CmsMasterPagedefSpotAccess {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsMasterPagedefSpot|null - Belongs to cms page template spot */
private ?CmsMasterPagedefSpot $cmsMasterPagedefSpot = null
, 
    // TCMSFieldVarchar
/** @var string - Module */
private string $model = ''  ) {}

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
public function getCmsMasterPagedefSpot(): ?CmsMasterPagedefSpot
{
    return $this->cmsMasterPagedefSpot;
}

public function setCmsMasterPagedefSpot(?CmsMasterPagedefSpot $cmsMasterPagedefSpot): self
{
    $this->cmsMasterPagedefSpot = $cmsMasterPagedefSpot;

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


  
}
