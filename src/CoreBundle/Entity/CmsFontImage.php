<?php
namespace ChameleonSystem\CoreBundle\Entity;


class CmsFontImage {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Profile name */
private string $profileName = '', 
    // TCMSFieldVarchar
/** @var string - Image height */
private string $imgHeight = '-1', 
    // TCMSFieldVarchar
/** @var string - Image width */
private string $imgWidth = '-1', 
    // TCMSFieldVarchar
/** @var string - Image background color */
private string $imgBackgroundColor = '-1', 
    // TCMSFieldVarchar
/** @var string - Font color */
private string $fontColor = '', 
    // TCMSFieldVarchar
/** @var string - Font size */
private string $fontSize = '', 
    // TCMSFieldVarchar
/** @var string - Font file */
private string $fontFilename = '', 
    // TCMSFieldVarchar
/** @var string - Background image file */
private string $backgroundImgFile = '', 
    // TCMSFieldVarchar
/** @var string - Text position X-axis */
private string $textPositionX = '', 
    // TCMSFieldVarchar
/** @var string - Text position Y-axis */
private string $textPositionY = ''  ) {}

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
public function getProfileName(): string
{
    return $this->profileName;
}
public function setProfileName(string $profileName): self
{
    $this->profileName = $profileName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getImgHeight(): string
{
    return $this->imgHeight;
}
public function setImgHeight(string $imgHeight): self
{
    $this->imgHeight = $imgHeight;

    return $this;
}


  
    // TCMSFieldVarchar
public function getImgWidth(): string
{
    return $this->imgWidth;
}
public function setImgWidth(string $imgWidth): self
{
    $this->imgWidth = $imgWidth;

    return $this;
}


  
    // TCMSFieldVarchar
public function getImgBackgroundColor(): string
{
    return $this->imgBackgroundColor;
}
public function setImgBackgroundColor(string $imgBackgroundColor): self
{
    $this->imgBackgroundColor = $imgBackgroundColor;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFontColor(): string
{
    return $this->fontColor;
}
public function setFontColor(string $fontColor): self
{
    $this->fontColor = $fontColor;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFontSize(): string
{
    return $this->fontSize;
}
public function setFontSize(string $fontSize): self
{
    $this->fontSize = $fontSize;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFontFilename(): string
{
    return $this->fontFilename;
}
public function setFontFilename(string $fontFilename): self
{
    $this->fontFilename = $fontFilename;

    return $this;
}


  
    // TCMSFieldVarchar
public function getBackgroundImgFile(): string
{
    return $this->backgroundImgFile;
}
public function setBackgroundImgFile(string $backgroundImgFile): self
{
    $this->backgroundImgFile = $backgroundImgFile;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTextPositionX(): string
{
    return $this->textPositionX;
}
public function setTextPositionX(string $textPositionX): self
{
    $this->textPositionX = $textPositionX;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTextPositionY(): string
{
    return $this->textPositionY;
}
public function setTextPositionY(string $textPositionY): self
{
    $this->textPositionY = $textPositionY;

    return $this;
}


  
}
