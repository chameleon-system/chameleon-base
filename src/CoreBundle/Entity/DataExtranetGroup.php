<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataExtranetGroup {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldBoolean
/** @var bool - Automatic assignment is active */
private bool $autoAssignActive = false, 
    // TCMSFieldDecimal
/** @var float - Auto assignment from order value */
private float $autoAssignOrderValueStart = 0, 
    // TCMSFieldDecimal
/** @var float - Auto assignment up to order value */
private float $autoAssignOrderValueEnd = 0  ) {}

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


  
    // TCMSFieldBoolean
public function isAutoAssignActive(): bool
{
    return $this->autoAssignActive;
}
public function setAutoAssignActive(bool $autoAssignActive): self
{
    $this->autoAssignActive = $autoAssignActive;

    return $this;
}


  
    // TCMSFieldDecimal
public function getAutoAssignOrderValueStart(): float
{
    return $this->autoAssignOrderValueStart;
}
public function setAutoAssignOrderValueStart(float $autoAssignOrderValueStart): self
{
    $this->autoAssignOrderValueStart = $autoAssignOrderValueStart;

    return $this;
}


  
    // TCMSFieldDecimal
public function getAutoAssignOrderValueEnd(): float
{
    return $this->autoAssignOrderValueEnd;
}
public function setAutoAssignOrderValueEnd(float $autoAssignOrderValueEnd): self
{
    $this->autoAssignOrderValueEnd = $autoAssignOrderValueEnd;

    return $this;
}


  
}
