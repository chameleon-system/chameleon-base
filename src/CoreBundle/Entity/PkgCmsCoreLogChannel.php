<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgCmsCoreLogChannel {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Maximum age of entries for this channel (in seconds) */
private string $maxLogAgeInSeconds = ''  ) {}

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
public function getMaxLogAgeInSeconds(): string
{
    return $this->maxLogAgeInSeconds;
}
public function setMaxLogAgeInSeconds(string $maxLogAgeInSeconds): self
{
    $this->maxLogAgeInSeconds = $maxLogAgeInSeconds;

    return $this;
}


  
}
