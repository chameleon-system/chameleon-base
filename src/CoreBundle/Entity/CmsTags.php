<?php
namespace ChameleonSystem\CoreBundle\Entity;


class CmsTags {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Tag */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - URL name */
private string $urlname = '', 
    // TCMSFieldVarchar
/** @var string - Quantity */
private string $count = '0'  ) {}

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
public function getUrlname(): string
{
    return $this->urlname;
}
public function setUrlname(string $urlname): self
{
    $this->urlname = $urlname;

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


  
}