<?php
namespace ChameleonSystem\CoreBundle\Entity;


class CmsRecordRevision {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Record ID */
private string $recordid = '', 
    // TCMSFieldVarchar
/** @var string - Title */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Version number */
private string $revisionNr = ''  ) {}

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
public function getRevisionNr(): string
{
    return $this->revisionNr;
}
public function setRevisionNr(string $revisionNr): self
{
    $this->revisionNr = $revisionNr;

    return $this;
}


  
}
