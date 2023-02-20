<?php
namespace ChameleonSystem\CoreBundle\Entity;


class ModuleList {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Title */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Sub headline */
private string $subHeadline = ''  ) {}

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
public function getSubHeadline(): string
{
    return $this->subHeadline;
}
public function setSubHeadline(string $subHeadline): self
{
    $this->subHeadline = $subHeadline;

    return $this;
}


  
}
