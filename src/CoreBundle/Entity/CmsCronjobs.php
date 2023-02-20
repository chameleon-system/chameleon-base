<?php
namespace ChameleonSystem\CoreBundle\Entity;


class CmsCronjobs {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Last executed on */
private string $lastExecution = '', 
    // TCMSFieldVarchar
/** @var string - Class name/service ID */
private string $cronClass = '', 
    // TCMSFieldVarchar
/** @var string - Class path */
private string $classSubtype = '', 
    // TCMSFieldVarchar
/** @var string - Reset lock after N minutes */
private string $unlockAfterNMinutes = '', 
    // TCMSFieldVarchar
/** @var string - Execute every N minutes */
private string $executeEveryNMinutes = '', 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = ''  ) {}

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
public function getLastExecution(): string
{
    return $this->lastExecution;
}
public function setLastExecution(string $lastExecution): self
{
    $this->lastExecution = $lastExecution;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCronClass(): string
{
    return $this->cronClass;
}
public function setCronClass(string $cronClass): self
{
    $this->cronClass = $cronClass;

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
public function getUnlockAfterNMinutes(): string
{
    return $this->unlockAfterNMinutes;
}
public function setUnlockAfterNMinutes(string $unlockAfterNMinutes): self
{
    $this->unlockAfterNMinutes = $unlockAfterNMinutes;

    return $this;
}


  
    // TCMSFieldVarchar
public function getExecuteEveryNMinutes(): string
{
    return $this->executeEveryNMinutes;
}
public function setExecuteEveryNMinutes(string $executeEveryNMinutes): self
{
    $this->executeEveryNMinutes = $executeEveryNMinutes;

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


  
}
