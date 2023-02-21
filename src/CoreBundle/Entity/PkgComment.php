<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgCommentType;
use ChameleonSystem\CoreBundle\Entity\DataExtranetUser;

class PkgComment {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var PkgCommentType|null - Comment type */
private ?PkgCommentType $pkgCommentType = null
, 
    // TCMSFieldVarchar
/** @var string - Object ID */
private string $itemId = '', 
    // TCMSFieldLookup
/** @var DataExtranetUser|null - User */
private ?DataExtranetUser $dataExtranetUser = null
, 
    // TCMSFieldLookup
/** @var PkgComment|null - Comment feedback */
private ?PkgComment $pkgComment = null
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
    // TCMSFieldLookup
public function getPkgCommentType(): ?PkgCommentType
{
    return $this->pkgCommentType;
}

public function setPkgCommentType(?PkgCommentType $pkgCommentType): self
{
    $this->pkgCommentType = $pkgCommentType;

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
public function getDataExtranetUser(): ?DataExtranetUser
{
    return $this->dataExtranetUser;
}

public function setDataExtranetUser(?DataExtranetUser $dataExtranetUser): self
{
    $this->dataExtranetUser = $dataExtranetUser;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgComment(): ?PkgComment
{
    return $this->pkgComment;
}

public function setPkgComment(?PkgComment $pkgComment): self
{
    $this->pkgComment = $pkgComment;

    return $this;
}


  
}
