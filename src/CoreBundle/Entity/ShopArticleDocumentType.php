<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleDocumentType {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Title / Headline */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemname = ''  ) {}

  public function getId(): ?string
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
public function getSystemname(): string
{
    return $this->systemname;
}
public function setSystemname(string $systemname): self
{
    $this->systemname = $systemname;

    return $this;
}


  
}
