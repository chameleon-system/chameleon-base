<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgTrackObject {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string -  */
private string $count = '0', 
    // TCMSFieldVarchar
/** @var string -  */
private string $tableName = '', 
    // TCMSFieldVarchar
/** @var string -  */
private string $ownerId = '', 
    // TCMSFieldVarchar
/** @var string -  */
private string $timeBlock = ''  ) {}

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
public function getCount(): string
{
    return $this->count;
}
public function setCount(string $count): self
{
    $this->count = $count;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTableName(): string
{
    return $this->tableName;
}
public function setTableName(string $tableName): self
{
    $this->tableName = $tableName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getOwnerId(): string
{
    return $this->ownerId;
}
public function setOwnerId(string $ownerId): self
{
    $this->ownerId = $ownerId;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTimeBlock(): string
{
    return $this->timeBlock;
}
public function setTimeBlock(string $timeBlock): self
{
    $this->timeBlock = $timeBlock;

    return $this;
}


  
}
