<?php
namespace ChameleonSystem\CoreBundle\Entity;


class DataAtomicLock {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string -  */
private string $lockkey = ''  ) {}

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
public function getLockkey(): string
{
    return $this->lockkey;
}
public function setLockkey(string $lockkey): self
{
    $this->lockkey = $lockkey;

    return $this;
}


  
}
