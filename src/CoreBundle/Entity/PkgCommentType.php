<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCommentType {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null - Table */
private \ChameleonSystem\CoreBundle\Entity\CmsTblConf|null $cmsTblConf = null,
/** @var null|string - Table */
private ?string $cmsTblConfId = null
, 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Class to be used for pkg_comment */
private string $pkgCommentClassName = '', 
    // TCMSFieldVarchar
/** @var string - Path to class for pkg_comment */
private string $pkgCommentClassSubType = '', 
    // TCMSFieldOption
/** @var string - Class type for pkg_comment */
private string $pkgCommentClassType = 'Customer', 
    // TCMSFieldVarchar
/** @var string - Class name */
private string $className = '', 
    // TCMSFieldVarchar
/** @var string - Class subtype */
private string $classSubType = 'pkgComment/objects/db/TPkgCommentType', 
    // TCMSFieldOption
/** @var string - Class type */
private string $classType = 'Core'  ) {}

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


  
    // TCMSFieldVarchar
public function getPkgCommentClassName(): string
{
    return $this->pkgCommentClassName;
}
public function setPkgCommentClassName(string $pkgCommentClassName): self
{
    $this->pkgCommentClassName = $pkgCommentClassName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getPkgCommentClassSubType(): string
{
    return $this->pkgCommentClassSubType;
}
public function setPkgCommentClassSubType(string $pkgCommentClassSubType): self
{
    $this->pkgCommentClassSubType = $pkgCommentClassSubType;

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



  
    // TCMSFieldOption
public function getPkgCommentClassType(): string
{
    return $this->pkgCommentClassType;
}
public function setPkgCommentClassType(string $pkgCommentClassType): self
{
    $this->pkgCommentClassType = $pkgCommentClassType;

    return $this;
}


  
    // TCMSFieldVarchar
public function getClassName(): string
{
    return $this->className;
}
public function setClassName(string $className): self
{
    $this->className = $className;

    return $this;
}


  
    // TCMSFieldVarchar
public function getClassSubType(): string
{
    return $this->classSubType;
}
public function setClassSubType(string $classSubType): self
{
    $this->classSubType = $classSubType;

    return $this;
}


  
    // TCMSFieldOption
public function getClassType(): string
{
    return $this->classType;
}
public function setClassType(string $classType): self
{
    $this->classType = $classType;

    return $this;
}


  
}
