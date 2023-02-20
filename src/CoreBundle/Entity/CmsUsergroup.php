<?php
namespace ChameleonSystem\CoreBundle\Entity;


class CmsUsergroup {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - German translation */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - CMS group ID */
private string $internalIdentifier = ''  ) {}

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
public function getInternalIdentifier(): string
{
    return $this->internalIdentifier;
}
public function setInternalIdentifier(string $internalIdentifier): self
{
    $this->internalIdentifier = $internalIdentifier;

    return $this;
}


  
}
