<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgTrackObjectHistory {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null -  */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser = null,
/** @var null|string -  */
private ?string $dataExtranetUserId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgTrackObject|null -  */
private \ChameleonSystem\CoreBundle\Entity\PkgTrackObject|null $pkgTrackObject = null,
/** @var null|string -  */
private ?string $pkgTrackObjectId = null
, 
    // TCMSFieldVarchar
/** @var string -  */
private string $tableName = '', 
    // TCMSFieldVarchar
/** @var string -  */
private string $ownerId = '', 
    // TCMSFieldDateTimeNow
/** @var \DateTime|null -  */
private \DateTime|null $datecreated = null, 
    // TCMSFieldVarchar
/** @var string -  */
private string $sessionId = '', 
    // TCMSFieldVarchar
/** @var string -  */
private string $ip = '', 
    // TCMSFieldVarchar
/** @var string -  */
private string $requestChecksum = '', 
    // TCMSFieldBoolean
/** @var bool -  */
private bool $itemCounted = false  ) {}

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
public function getTableName(): string
{
    return $this->tableName;
}
public function setTableName(string $tableName): self
{
    $this->tableName = $tableName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getOwnerId(): string
{
    return $this->ownerId;
}
public function setOwnerId(string $ownerId): self
{
    $this->ownerId = $ownerId;

    return $this;
}


  
    // TCMSFieldDateTimeNow
public function getDatecreated(): \DateTime|null
{
    return $this->datecreated;
}
public function setDatecreated(\DateTime|null $datecreated): self
{
    $this->datecreated = $datecreated;

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



  
    // TCMSFieldVarchar
public function getSessionId(): string
{
    return $this->sessionId;
}
public function setSessionId(string $sessionId): self
{
    $this->sessionId = $sessionId;

    return $this;
}


  
    // TCMSFieldVarchar
public function getIp(): string
{
    return $this->ip;
}
public function setIp(string $ip): self
{
    $this->ip = $ip;

    return $this;
}


  
    // TCMSFieldVarchar
public function getRequestChecksum(): string
{
    return $this->requestChecksum;
}
public function setRequestChecksum(string $requestChecksum): self
{
    $this->requestChecksum = $requestChecksum;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgTrackObject(): \ChameleonSystem\CoreBundle\Entity\PkgTrackObject|null
{
    return $this->pkgTrackObject;
}
public function setPkgTrackObject(\ChameleonSystem\CoreBundle\Entity\PkgTrackObject|null $pkgTrackObject): self
{
    $this->pkgTrackObject = $pkgTrackObject;
    $this->pkgTrackObjectId = $pkgTrackObject?->getId();

    return $this;
}
public function getPkgTrackObjectId(): ?string
{
    return $this->pkgTrackObjectId;
}
public function setPkgTrackObjectId(?string $pkgTrackObjectId): self
{
    $this->pkgTrackObjectId = $pkgTrackObjectId;
    // todo - load new id
    //$this->pkgTrackObjectId = $?->getId();

    return $this;
}



  
    // TCMSFieldBoolean
public function isItemCounted(): bool
{
    return $this->itemCounted;
}
public function setItemCounted(bool $itemCounted): self
{
    $this->itemCounted = $itemCounted;

    return $this;
}


  
}
