<?php
namespace ChameleonSystem\CoreBundle\Entity;


class CmsTplModule {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Icon font CSS class */
private string $iconFontCssClass = '', 
    // TCMSFieldVarchar
/** @var string - Module name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Class name / service ID */
private string $classname = ''  ) {}

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
public function getIconFontCssClass(): string
{
    return $this->iconFontCssClass;
}
public function setIconFontCssClass(string $iconFontCssClass): self
{
    $this->iconFontCssClass = $iconFontCssClass;

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
public function getClassname(): string
{
    return $this->classname;
}
public function setClassname(string $classname): self
{
    $this->classname = $classname;

    return $this;
}


  
}
