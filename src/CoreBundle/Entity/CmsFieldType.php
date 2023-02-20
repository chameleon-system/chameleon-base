<?php
namespace ChameleonSystem\CoreBundle\Entity;


class CmsFieldType {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $trans = '', 
    // TCMSFieldVarchar
/** @var string - PHP class subtype */
private string $classSubtype = '', 
    // TCMSFieldVarchar
/** @var string - Field code name */
private string $constname = '', 
    // TCMSFieldVarchar
/** @var string - MySQL data type */
private string $mysqlType = '', 
    // TCMSFieldVarchar
/** @var string - MySQL field length or value list (ENUM) */
private string $lengthSet = '', 
    // TCMSFieldVarchar
/** @var string - Default value */
private string $mysqlStandardValue = '', 
    // TCMSFieldVarchar
/** @var string - PHP class */
private string $fieldclass = ''  ) {}

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
public function getTrans(): string
{
    return $this->trans;
}
public function setTrans(string $trans): self
{
    $this->trans = $trans;

    return $this;
}


  
    // TCMSFieldVarchar
public function getClassSubtype(): string
{
    return $this->classSubtype;
}
public function setClassSubtype(string $classSubtype): self
{
    $this->classSubtype = $classSubtype;

    return $this;
}


  
    // TCMSFieldVarchar
public function getConstname(): string
{
    return $this->constname;
}
public function setConstname(string $constname): self
{
    $this->constname = $constname;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMysqlType(): string
{
    return $this->mysqlType;
}
public function setMysqlType(string $mysqlType): self
{
    $this->mysqlType = $mysqlType;

    return $this;
}


  
    // TCMSFieldVarchar
public function getLengthSet(): string
{
    return $this->lengthSet;
}
public function setLengthSet(string $lengthSet): self
{
    $this->lengthSet = $lengthSet;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMysqlStandardValue(): string
{
    return $this->mysqlStandardValue;
}
public function setMysqlStandardValue(string $mysqlStandardValue): self
{
    $this->mysqlStandardValue = $mysqlStandardValue;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFieldclass(): string
{
    return $this->fieldclass;
}
public function setFieldclass(string $fieldclass): self
{
    $this->fieldclass = $fieldclass;

    return $this;
}


  
}
