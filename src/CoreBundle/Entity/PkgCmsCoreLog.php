<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsCoreLog {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null - Extranet user ID */
private \ChameleonSystem\CoreBundle\Entity\DataExtranetUser|null $dataExtranetUser = null,
/** @var null|string - Extranet user ID */
private ?string $dataExtranetUserId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsUser|null - CMS user */
private \ChameleonSystem\CoreBundle\Entity\CmsUser|null $cmsUser = null,
/** @var null|string - CMS user */
private ?string $cmsUserId = null
, 
    // TCMSFieldNumber
/** @var int - Time stamp */
private int $timestamp = 0, 
    // TCMSFieldVarchar
/** @var string - Channel */
private string $channel = '', 
    // TCMSFieldNumber
/** @var int -  */
private int $level = 0, 
    // TCMSFieldVarchar
/** @var string - Message */
private string $message = '', 
    // TCMSFieldVarchar
/** @var string - User session ID */
private string $session = '', 
    // TCMSFieldVarchar
/** @var string - Request ID */
private string $uid = '', 
    // TCMSFieldVarchar
/** @var string - File name */
private string $file = '', 
    // TCMSFieldNumber
/** @var int - Line */
private int $line = 0, 
    // TCMSFieldVarchar
/** @var string - Request URL */
private string $requestUrl = '', 
    // TCMSFieldVarchar
/** @var string -  */
private string $referrerUrl = '', 
    // TCMSFieldOption
/** @var string - HTTP method */
private string $httpMethod = '', 
    // TCMSFieldVarchar
/** @var string - Server name */
private string $server = '', 
    // TCMSFieldVarchar
/** @var string - Client IP address */
private string $ip = '', 
    // TCMSFieldVarchar
/** @var string - Extranet user login */
private string $dataExtranetUserName = '', 
    // TCMSFieldBlob
/** @var string -  */
private string $context = ''  ) {}

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
public function getTimestamp(): int
{
    return $this->timestamp;
}
public function setTimestamp(int $timestamp): self
{
    $this->timestamp = $timestamp;

    return $this;
}


  
    // TCMSFieldVarchar
public function getChannel(): string
{
    return $this->channel;
}
public function setChannel(string $channel): self
{
    $this->channel = $channel;

    return $this;
}


  
    // TCMSFieldNumber
public function getLevel(): int
{
    return $this->level;
}
public function setLevel(int $level): self
{
    $this->level = $level;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMessage(): string
{
    return $this->message;
}
public function setMessage(string $message): self
{
    $this->message = $message;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSession(): string
{
    return $this->session;
}
public function setSession(string $session): self
{
    $this->session = $session;

    return $this;
}


  
    // TCMSFieldVarchar
public function getUid(): string
{
    return $this->uid;
}
public function setUid(string $uid): self
{
    $this->uid = $uid;

    return $this;
}


  
    // TCMSFieldVarchar
public function getFile(): string
{
    return $this->file;
}
public function setFile(string $file): self
{
    $this->file = $file;

    return $this;
}


  
    // TCMSFieldNumber
public function getLine(): int
{
    return $this->line;
}
public function setLine(int $line): self
{
    $this->line = $line;

    return $this;
}


  
    // TCMSFieldVarchar
public function getRequestUrl(): string
{
    return $this->requestUrl;
}
public function setRequestUrl(string $requestUrl): self
{
    $this->requestUrl = $requestUrl;

    return $this;
}


  
    // TCMSFieldVarchar
public function getReferrerUrl(): string
{
    return $this->referrerUrl;
}
public function setReferrerUrl(string $referrerUrl): self
{
    $this->referrerUrl = $referrerUrl;

    return $this;
}


  
    // TCMSFieldOption
public function getHttpMethod(): string
{
    return $this->httpMethod;
}
public function setHttpMethod(string $httpMethod): self
{
    $this->httpMethod = $httpMethod;

    return $this;
}


  
    // TCMSFieldVarchar
public function getServer(): string
{
    return $this->server;
}
public function setServer(string $server): self
{
    $this->server = $server;

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
public function getDataExtranetUserName(): string
{
    return $this->dataExtranetUserName;
}
public function setDataExtranetUserName(string $dataExtranetUserName): self
{
    $this->dataExtranetUserName = $dataExtranetUserName;

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



  
    // TCMSFieldBlob
public function getContext(): string
{
    return $this->context;
}
public function setContext(string $context): self
{
    $this->context = $context;

    return $this;
}


  
}
