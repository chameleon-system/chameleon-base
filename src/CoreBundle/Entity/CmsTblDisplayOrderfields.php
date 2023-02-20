<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTblDisplayOrderfields {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null - Belongs to table */
private \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null $cmsTblConf = null,
/** @var null|string - Belongs to table */
private ?string $cmsTblConfId = null
, 
    // TCMSFieldVarchar
/** @var string - Database field name */
private string $name = '', 
    // TCMSFieldOption
/** @var string - Order direction */
private string $sortOrderDirection = 'ASC', 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0  ) {}

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


  
    // TCMSFieldOption
public function getSortOrderDirection(): string
{
    return $this->sortOrderDirection;
}
public function setSortOrderDirection(string $sortOrderDirection): self
{
    $this->sortOrderDirection = $sortOrderDirection;

    return $this;
}


  
    // TCMSFieldPosition
public function getPosition(): int
{
    return $this->position;
}
public function setPosition(int $position): self
{
    $this->position = $position;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsTblConf(): \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null
{
    return $this->cmsTblConf;
}
public function setCmsTblConf(\ChameleonSystem\CoreBundle\Entity\CmsTblConf|null $cmsTblConf): self
{
    $this->cmsTblConf = $cmsTblConf;
    $this->cmsTblConfId = $cmsTblConf?->getId();

    return $this;
}
public function getCmsTblConfId(): ?string
{
    return $this->cmsTblConfId;
}
public function setCmsTblConfId(?string $cmsTblConfId): self
{
    $this->cmsTblConfId = $cmsTblConfId;
    // todo - load new id
    //$this->cmsTblConfId = $?->getId();

    return $this;
}



  
}
