<?php
namespace ChameleonSystem\CoreBundle\Entity;


class CmsImageCropPreset {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Width */
private string $width = '0', 
    // TCMSFieldVarchar
/** @var string - Height */
private string $height = '0', 
    // TCMSFieldVarchar
/** @var string - System name */
private string $systemName = ''  ) {}

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
public function getWidth(): string
{
    return $this->width;
}
public function setWidth(string $width): self
{
    $this->width = $width;

    return $this;
}


  
    // TCMSFieldVarchar
public function getHeight(): string
{
    return $this->height;
}
public function setHeight(string $height): self
{
    $this->height = $height;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSystemName(): string
{
    return $this->systemName;
}
public function setSystemName(string $systemName): self
{
    $this->systemName = $systemName;

    return $this;
}


  
}
