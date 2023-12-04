<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTblExtension {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null - Text template */
private \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null $cmsTblConf = null,
/** @var null|string - Text template */
private ?string $cmsTblConfId = null
, 
    // TCMSFieldVarchar
/** @var string - Classname */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - List class extension */
private string $nameList = '', 
    // TCMSFieldVarchar
/** @var string - Subtype */
private string $subtype = 'dbobjects', 
    // TCMSFieldOption
/** @var string - Type */
private string $type = 'Customer', 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0, 
    // TCMSFieldVarchar
/** @var string - Name of the last extension before Tadb* */
private string $virtualItemClassName = '', 
    // TCMSFieldVarchar
/** @var string - Name of the last extension before Tadb*List */
private string $virtualItemClassListName = ''  ) {}

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
public function getNameList(): string
{
    return $this->nameList;
}
public function setNameList(string $nameList): self
{
    $this->nameList = $nameList;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSubtype(): string
{
    return $this->subtype;
}
public function setSubtype(string $subtype): self
{
    $this->subtype = $subtype;

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


  
    // TCMSFieldVarchar
public function getVirtualItemClassName(): string
{
    return $this->virtualItemClassName;
}
public function setVirtualItemClassName(string $virtualItemClassName): self
{
    $this->virtualItemClassName = $virtualItemClassName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getVirtualItemClassListName(): string
{
    return $this->virtualItemClassListName;
}
public function setVirtualItemClassListName(string $virtualItemClassListName): self
{
    $this->virtualItemClassListName = $virtualItemClassListName;

    return $this;
}


  
}
