<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsFiletype;
use ChameleonSystem\CoreBundle\Entity\CmsUser;

class CmsMedia {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Height */
private string $height = '0', 
    // TCMSFieldLookup
/** @var CmsFiletype|null - Image type */
private ?CmsFiletype $cmsFiletype = null
, 
    // TCMSFieldVarchar
/** @var string - File size */
private string $filesize = '', 
    // TCMSFieldVarchar
/** @var string - Width */
private string $width = '0', 
    // TCMSFieldVarchar
/** @var string - Title / Description */
private string $description = '', 
    // TCMSFieldVarchar
/** @var string - Supported file types */
private string $filetypes = '', 
    // TCMSFieldVarchar
/** @var string - Alt tag */
private string $altTag = '', 
    // TCMSFieldVarchar
/** @var string - Systemname */
private string $systemname = '', 
    // TCMSFieldVarchar
/** @var string - Custom file name */
private string $customFilename = '', 
    // TCMSFieldVarchar
/** @var string - Path */
private string $path = '', 
    // TCMSFieldLookup
/** @var CmsMedia|null - Preview image */
private ?CmsMedia $cmsMedia = null
, 
    // TCMSFieldVarchar
/** @var string - Refresh Token */
private string $refreshToken = '', 
    // TCMSFieldLookup
/** @var CmsUser|null - Last changed by */
private ?CmsUser $cmsUser = null
  ) {}

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
public function getHeight(): string
{
    return $this->height;
}
public function setHeight(string $height): self
{
    $this->height = $height;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsFiletype(): ?CmsFiletype
{
    return $this->cmsFiletype;
}

public function setCmsFiletype(?CmsFiletype $cmsFiletype): self
{
    $this->cmsFiletype = $cmsFiletype;

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
public function getDescription(): string
{
    return $this->description;
}
public function setDescription(string $description): self
{
    $this->description = $description;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFiletypes(): string
{
    return $this->filetypes;
}
public function setFiletypes(string $filetypes): self
{
    $this->filetypes = $filetypes;

    return $this;
}


  
    // TCMSFieldVarchar
public function getAltTag(): string
{
    return $this->altTag;
}
public function setAltTag(string $altTag): self
{
    $this->altTag = $altTag;

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


  
    // TCMSFieldVarchar
public function getCustomFilename(): string
{
    return $this->customFilename;
}
public function setCustomFilename(string $customFilename): self
{
    $this->customFilename = $customFilename;

    return $this;
}


  
    // TCMSFieldVarchar
public function getPath(): string
{
    return $this->path;
}
public function setPath(string $path): self
{
    $this->path = $path;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsMedia(): ?CmsMedia
{
    return $this->cmsMedia;
}

public function setCmsMedia(?CmsMedia $cmsMedia): self
{
    $this->cmsMedia = $cmsMedia;

    return $this;
}


  
    // TCMSFieldVarchar
public function getRefreshToken(): string
{
    return $this->refreshToken;
}
public function setRefreshToken(string $refreshToken): self
{
    $this->refreshToken = $refreshToken;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsUser(): ?CmsUser
{
    return $this->cmsUser;
}

public function setCmsUser(?CmsUser $cmsUser): self
{
    $this->cmsUser = $cmsUser;

    return $this;
}


  
}
