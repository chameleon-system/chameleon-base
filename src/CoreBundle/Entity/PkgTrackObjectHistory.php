<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgTrackObject;

class PkgTrackObjectHistory {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string -  */
private string $tableName = '', 
    // TCMSFieldVarchar
/** @var string -  */
private string $ownerId = '', 
    // TCMSFieldVarchar
/** @var string -  */
private string $sessionId = '', 
    // TCMSFieldVarchar
/** @var string -  */
private string $ip = '', 
    // TCMSFieldVarchar
/** @var string -  */
private string $requestChecksum = '', 
    // TCMSFieldLookupParentID
/** @var PkgTrackObject|null -  */
private ?PkgTrackObject $pkgTrackObject = null
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


  
    // TCMSFieldLookupParentID
public function getPkgTrackObject(): ?PkgTrackObject
{
    return $this->pkgTrackObject;
}

public function setPkgTrackObject(?PkgTrackObject $pkgTrackObject): self
{
    $this->pkgTrackObject = $pkgTrackObject;

    return $this;
}


  
}
