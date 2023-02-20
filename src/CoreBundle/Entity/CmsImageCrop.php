<?php
namespace ChameleonSystem\CoreBundle\Entity;


class CmsImageCrop {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - X position of crop */
private string $posX = '0', 
    // TCMSFieldVarchar
/** @var string - Y position of crop */
private string $posY = '0', 
    // TCMSFieldVarchar
/** @var string -  */
private string $width = '0', 
    // TCMSFieldVarchar
/** @var string - Crop height */
private string $height = '0', 
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = ''  ) {}

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
public function getPosX(): string
{
    return $this->posX;
}
public function setPosX(string $posX): self
{
    $this->posX = $posX;

    return $this;
}


  
    // TCMSFieldVarchar
public function getPosY(): string
{
    return $this->posY;
}
public function setPosY(string $posY): self
{
    $this->posY = $posY;

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
public function getName(): string
{
    return $this->name;
}
public function setName(string $name): self
{
    $this->name = $name;

    return $this;
}


  
}
