<?php
namespace ChameleonSystem\CoreBundle\Entity;


class CmsRole {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - CMS role abbreviation */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - German translation */
private string $trans = ''  ) {}

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
public function getTrans(): string
{
    return $this->trans;
}
public function setTrans(string $trans): self
{
    $this->trans = $trans;

    return $this;
}


  
}
