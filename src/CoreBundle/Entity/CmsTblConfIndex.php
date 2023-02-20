<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTblConfIndex {
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
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Field list */
private string $definition = '', 
    // TCMSFieldOption
/** @var string - Index type */
private string $type = 'INDEX'  ) {}

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
public function getDefinition(): string
{
    return $this->definition;
}
public function setDefinition(string $definition): self
{
    $this->definition = $definition;

    return $this;
}


  
    // TCMSFieldOption
public function getType(): string
{
    return $this->type;
}
public function setType(string $type): self
{
    $this->type = $type;

    return $this;
}


  
}
