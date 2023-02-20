<?php
namespace ChameleonSystem\CoreBundle\Entity;


class CmsDocument {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - File name */
private string $filename = '', 
    // TCMSFieldVarchar
/** @var string - Image width */
private string $hiddenImageWidth = '', 
    // TCMSFieldVarchar
/** @var string - Image height */
private string $hiddenImageHeight = '', 
    // TCMSFieldVarchar
/** @var string - User downloads */
private string $counter = '', 
    // TCMSFieldVarchar
/** @var string - File size */
private string $filesize = '', 
    // TCMSFieldVarchar
/** @var string - SEO Name */
private string $seoName = ''  ) {}

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
public function getFilename(): string
{
    return $this->filename;
}
public function setFilename(string $filename): self
{
    $this->filename = $filename;

    return $this;
}


  
    // TCMSFieldVarchar
public function getHiddenImageWidth(): string
{
    return $this->hiddenImageWidth;
}
public function setHiddenImageWidth(string $hiddenImageWidth): self
{
    $this->hiddenImageWidth = $hiddenImageWidth;

    return $this;
}


  
    // TCMSFieldVarchar
public function getHiddenImageHeight(): string
{
    return $this->hiddenImageHeight;
}
public function setHiddenImageHeight(string $hiddenImageHeight): self
{
    $this->hiddenImageHeight = $hiddenImageHeight;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCounter(): string
{
    return $this->counter;
}
public function setCounter(string $counter): self
{
    $this->counter = $counter;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFilesize(): string
{
    return $this->filesize;
}
public function setFilesize(string $filesize): self
{
    $this->filesize = $filesize;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSeoName(): string
{
    return $this->seoName;
}
public function setSeoName(string $seoName): self
{
    $this->seoName = $seoName;

    return $this;
}


  
}
