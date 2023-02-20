<?php
namespace ChameleonSystem\CoreBundle\Entity;


class CmsLock {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Record ID */
private string $recordid = ''  ) {}

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
public function getRecordid(): string
{
    return $this->recordid;
}
public function setRecordid(string $recordid): self
{
    $this->recordid = $recordid;

    return $this;
}


  
}
