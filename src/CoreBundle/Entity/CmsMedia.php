<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMedia {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsFiletype|null - Image type */
private \ChameleonSystem\CoreBundle\Entity\CmsFiletype|null $cmsFiletype = null,
/** @var null|string - Image type */
private ?string $cmsFiletypeId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Preview image */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia = null,
/** @var null|string - Preview image */
private ?string $cmsMediaId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsUser|null - Last changed by */
private \ChameleonSystem\CoreBundle\Entity\CmsUser|null $cmsUser = null,
/** @var null|string - Last changed by */
private ?string $cmsUserId = null
, 
    // TCMSFieldNumber
/** @var int - Height */
private int $height = 0, 
    // TCMSFieldNumber
/** @var int - File size */
private int $filesize = 0, 
    // TCMSFieldTreeNode
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Image category */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $cmsMediaTreeId = null, 
    // TCMSFieldNumber
/** @var int - Width */
private int $width = 0, 
    // TCMSFieldVarchar
/** @var string - Title / Description */
private string $description = '', 
    // TCMSFieldText
/** @var string - Keywords / Tags */
private string $metatags = '', 
    // TCMSFieldVarchar
/** @var string - Supported file types */
private string $filetypes = '', 
    // TCMSFieldVarchar
/** @var string - Alt tag */
private string $altTag = '', 
    // TCMSFieldVarchar
/** @var string - Systemname */
private string $systemname = '', 
    // TCMSFieldLookupMultiselectTags
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTags[] - Tags */
private \Doctrine\Common\Collections\Collection $cmsTagsMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldVarchar
/** @var string - Custom file name */
private string $customFilename = '', 
    // TCMSFieldMediaPath
/** @var string - Path */
private string $path = '', 
    // TCMSFieldExternalVideoCode
/** @var string - Video HTML code */
private string $externalEmbedCode = '', 
    // TCMSFieldText
/** @var string - Thumbnail of an external video */
private string $externalVideoThumbnail = '', 
    // TCMSFieldTimestamp
/** @var \DateTime|null - Last changed on */
private \DateTime|null $timeStamp = null, 
    // TCMSFieldDateTimeNow
/** @var \DateTime|null - Last changed */
private \DateTime|null $dateChanged = null, 
    // TCMSFieldVarchar
/** @var string - Refresh Token */
private string $refreshToken = '', 
    // TCMSFieldExternalVideoID
/** @var string - Video ID with external host */
private string $externalVideoId = ''  ) {}

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
    // TCMSFieldNumber
public function getHeight(): int
{
    return $this->height;
}
public function setHeight(int $height): self
{
    $this->height = $height;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsFiletype(): \ChameleonSystem\CoreBundle\Entity\CmsFiletype|null
{
    return $this->cmsFiletype;
}
public function setCmsFiletype(\ChameleonSystem\CoreBundle\Entity\CmsFiletype|null $cmsFiletype): self
{
    $this->cmsFiletype = $cmsFiletype;
    $this->cmsFiletypeId = $cmsFiletype?->getId();

    return $this;
}
public function getCmsFiletypeId(): ?string
{
    return $this->cmsFiletypeId;
}
public function setCmsFiletypeId(?string $cmsFiletypeId): self
{
    $this->cmsFiletypeId = $cmsFiletypeId;
    // todo - load new id
    //$this->cmsFiletypeId = $?->getId();

    return $this;
}



  
    // TCMSFieldNumber
public function getFilesize(): int
{
    return $this->filesize;
}
public function setFilesize(int $filesize): self
{
    $this->filesize = $filesize;

    return $this;
}


  
    // TCMSFieldTreeNode
public function getCmsMediaTreeId(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->cmsMediaTreeId;
}
public function setCmsMediaTreeId(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $cmsMediaTreeId): self
{
    $this->cmsMediaTreeId = $cmsMediaTreeId;

    return $this;
}


  
    // TCMSFieldNumber
public function getWidth(): int
{
    return $this->width;
}
public function setWidth(int $width): self
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


  
    // TCMSFieldText
public function getMetatags(): string
{
    return $this->metatags;
}
public function setMetatags(string $metatags): self
{
    $this->metatags = $metatags;

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


  
    // TCMSFieldLookupMultiselectTags
public function getCmsTagsMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsTagsMlt;
}
public function setCmsTagsMlt(\Doctrine\Common\Collections\Collection $cmsTagsMlt): self
{
    $this->cmsTagsMlt = $cmsTagsMlt;

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


  
    // TCMSFieldMediaPath
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
public function getCmsMedia(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->cmsMedia;
}
public function setCmsMedia(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia): self
{
    $this->cmsMedia = $cmsMedia;
    $this->cmsMediaId = $cmsMedia?->getId();

    return $this;
}
public function getCmsMediaId(): ?string
{
    return $this->cmsMediaId;
}
public function setCmsMediaId(?string $cmsMediaId): self
{
    $this->cmsMediaId = $cmsMediaId;
    // todo - load new id
    //$this->cmsMediaId = $?->getId();

    return $this;
}



  
    // TCMSFieldExternalVideoCode
public function getExternalEmbedCode(): string
{
    return $this->externalEmbedCode;
}
public function setExternalEmbedCode(string $externalEmbedCode): self
{
    $this->externalEmbedCode = $externalEmbedCode;

    return $this;
}


  
    // TCMSFieldText
public function getExternalVideoThumbnail(): string
{
    return $this->externalVideoThumbnail;
}
public function setExternalVideoThumbnail(string $externalVideoThumbnail): self
{
    $this->externalVideoThumbnail = $externalVideoThumbnail;

    return $this;
}


  
    // TCMSFieldTimestamp
public function getTimeStamp(): \DateTime|null
{
    return $this->timeStamp;
}
public function setTimeStamp(\DateTime|null $timeStamp): self
{
    $this->timeStamp = $timeStamp;

    return $this;
}


  
    // TCMSFieldDateTimeNow
public function getDateChanged(): \DateTime|null
{
    return $this->dateChanged;
}
public function setDateChanged(\DateTime|null $dateChanged): self
{
    $this->dateChanged = $dateChanged;

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
public function getCmsUser(): \ChameleonSystem\CoreBundle\Entity\CmsUser|null
{
    return $this->cmsUser;
}
public function setCmsUser(\ChameleonSystem\CoreBundle\Entity\CmsUser|null $cmsUser): self
{
    $this->cmsUser = $cmsUser;
    $this->cmsUserId = $cmsUser?->getId();

    return $this;
}
public function getCmsUserId(): ?string
{
    return $this->cmsUserId;
}
public function setCmsUserId(?string $cmsUserId): self
{
    $this->cmsUserId = $cmsUserId;
    // todo - load new id
    //$this->cmsUserId = $?->getId();

    return $this;
}



  
    // TCMSFieldExternalVideoID
public function getExternalVideoId(): string
{
    return $this->externalVideoId;
}
public function setExternalVideoId(string $externalVideoId): self
{
    $this->externalVideoId = $externalVideoId;

    return $this;
}


  
}
