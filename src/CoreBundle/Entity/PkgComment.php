<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgComment {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgCommentType|null - Comment type */
private \ChameleonSystem\CoreBundle\Entity\PkgCommentType|null $pkgCommentType = null,
/** @var null|string - Comment type */
private ?string $pkgCommentTypeId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null - User */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser = null,
/** @var null|string - User */
private ?string $dataExtranetUserId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgComment|null - Comment feedback */
private \ChameleonSystem\CoreBundle\Entity\PkgComment|null $pkgComment = null,
/** @var null|string - Comment feedback */
private ?string $pkgCommentId = null
, 
    // TCMSFieldVarchar
/** @var string - Object ID */
private string $itemId = '', 
    // TCMSFieldDateTime
/** @var \DateTime|null - Creation date */
private \DateTime|null $createdTimestamp = null, 
    // TCMSFieldText
/** @var string - Comment text */
private string $comment = '', 
    // TCMSFieldBoolean
/** @var bool - Comment has been deleted */
private bool $markAsDeleted = false, 
    // TCMSFieldBoolean
/** @var bool - Comment has been reported */
private bool $markAsReported = false  ) {}

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
    // TCMSFieldLookup
public function getPkgCommentType(): \ChameleonSystem\CoreBundle\Entity\PkgCommentType|null
{
    return $this->pkgCommentType;
}
public function setPkgCommentType(\ChameleonSystem\CoreBundle\Entity\PkgCommentType|null $pkgCommentType): self
{
    $this->pkgCommentType = $pkgCommentType;
    $this->pkgCommentTypeId = $pkgCommentType?->getId();

    return $this;
}
public function getPkgCommentTypeId(): ?string
{
    return $this->pkgCommentTypeId;
}
public function setPkgCommentTypeId(?string $pkgCommentTypeId): self
{
    $this->pkgCommentTypeId = $pkgCommentTypeId;
    // todo - load new id
    //$this->pkgCommentTypeId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getItemId(): string
{
    return $this->itemId;
}
public function setItemId(string $itemId): self
{
    $this->itemId = $itemId;

    return $this;
}


  
    // TCMSFieldLookup
public function getDataExtranetUser(): \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null
{
    return $this->dataExtranetUser;
}
public function setDataExtranetUser(\ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser): self
{
    $this->dataExtranetUser = $dataExtranetUser;
    $this->dataExtranetUserId = $dataExtranetUser?->getId();

    return $this;
}
public function getDataExtranetUserId(): ?string
{
    return $this->dataExtranetUserId;
}
public function setDataExtranetUserId(?string $dataExtranetUserId): self
{
    $this->dataExtranetUserId = $dataExtranetUserId;
    // todo - load new id
    //$this->dataExtranetUserId = $?->getId();

    return $this;
}



  
    // TCMSFieldDateTime
public function getCreatedTimestamp(): \DateTime|null
{
    return $this->createdTimestamp;
}
public function setCreatedTimestamp(\DateTime|null $createdTimestamp): self
{
    $this->createdTimestamp = $createdTimestamp;

    return $this;
}


  
    // TCMSFieldText
public function getComment(): string
{
    return $this->comment;
}
public function setComment(string $comment): self
{
    $this->comment = $comment;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgComment(): \ChameleonSystem\CoreBundle\Entity\PkgComment|null
{
    return $this->pkgComment;
}
public function setPkgComment(\ChameleonSystem\CoreBundle\Entity\PkgComment|null $pkgComment): self
{
    $this->pkgComment = $pkgComment;
    $this->pkgCommentId = $pkgComment?->getId();

    return $this;
}
public function getPkgCommentId(): ?string
{
    return $this->pkgCommentId;
}
public function setPkgCommentId(?string $pkgCommentId): self
{
    $this->pkgCommentId = $pkgCommentId;
    // todo - load new id
    //$this->pkgCommentId = $?->getId();

    return $this;
}



  
    // TCMSFieldBoolean
public function isMarkAsDeleted(): bool
{
    return $this->markAsDeleted;
}
public function setMarkAsDeleted(bool $markAsDeleted): self
{
    $this->markAsDeleted = $markAsDeleted;

    return $this;
}


  
    // TCMSFieldBoolean
public function isMarkAsReported(): bool
{
    return $this->markAsReported;
}
public function setMarkAsReported(bool $markAsReported): self
{
    $this->markAsReported = $markAsReported;

    return $this;
}


  
}
