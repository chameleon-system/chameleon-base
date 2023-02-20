<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgCmsTheme {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Descriptive name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Own LESS file */
private string $lessFile = ''  ) {}

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
public function getLessFile(): string
{
    return $this->lessFile;
}
public function setLessFile(string $lessFile): self
{
    $this->lessFile = $lessFile;

    return $this;
}


  
}
