<?php
namespace ChameleonSystem\CoreBundle\Entity;


class ModuleText {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Headline */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Sub headline */
private string $subheadline = ''  ) {}

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
public function getSubheadline(): string
{
    return $this->subheadline;
}
public function setSubheadline(string $subheadline): self
{
    $this->subheadline = $subheadline;

    return $this;
}


  
}
