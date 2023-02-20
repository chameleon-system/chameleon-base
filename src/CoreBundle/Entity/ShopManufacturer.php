<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopManufacturer {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldBoolean
/** @var bool - Active */
private bool $active = true, 
    // TCMSFieldPosition
/** @var int - Position */
private int $position = 0, 
    // TCMSFieldVarchar
/** @var string - Short description */
private string $descriptionShort = '', 
    // TCMSFieldMedia
/** @var array<string> - Icon / logo */
private array $cmsMediaId = [], 
    // TCMSFieldColorpicker
/** @var string - Color */
private string $color = '', 
    // TCMSFieldVarchar
/** @var string - CSS file for manufacturer page */
private string $css = '', 
    // TCMSFieldWYSIWYG
/** @var string - Description */
private string $description = '', 
    // TCMSFieldWYSIWYG
/** @var string - Size chart */
private string $sizetable = ''  ) {}

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


  
    // TCMSFieldBoolean
public function isActive(): bool
{
    return $this->active;
}
public function setActive(bool $active): self
{
    $this->active = $active;

    return $this;
}


  
    // TCMSFieldPosition
public function getPosition(): int
{
    return $this->position;
}
public function setPosition(int $position): self
{
    $this->position = $position;

    return $this;
}


  
    // TCMSFieldVarchar
public function getDescriptionShort(): string
{
    return $this->descriptionShort;
}
public function setDescriptionShort(string $descriptionShort): self
{
    $this->descriptionShort = $descriptionShort;

    return $this;
}


  
    // TCMSFieldMedia
public function getCmsMediaId(): array
{
    return $this->cmsMediaId;
}
public function setCmsMediaId(array $cmsMediaId): self
{
    $this->cmsMediaId = $cmsMediaId;

    return $this;
}


  
    // TCMSFieldColorpicker
public function getColor(): string
{
    return $this->color;
}
public function setColor(string $color): self
{
    $this->color = $color;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCss(): string
{
    return $this->css;
}
public function setCss(string $css): self
{
    $this->css = $css;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getDescription(): string
{
    return $this->description;
}
public function setDescription(string $description): self
{
    $this->description = $description;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getSizetable(): string
{
    return $this->sizetable;
}
public function setSizetable(string $sizetable): self
{
    $this->sizetable = $sizetable;

    return $this;
}


  
}
