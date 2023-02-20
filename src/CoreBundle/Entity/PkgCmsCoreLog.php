<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgCmsCoreLog {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Time stamp */
private string $timestamp = '', 
    // TCMSFieldVarchar
/** @var string - Channel */
private string $channel = '', 
    // TCMSFieldVarchar
/** @var string -  */
private string $level = '', 
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
    // TCMSFieldVarchar
/** @var string - Line */
private string $line = '', 
    // TCMSFieldVarchar
/** @var string - Request URL */
private string $requestUrl = '', 
    // TCMSFieldVarchar
/** @var string -  */
private string $referrerUrl = '', 
    // TCMSFieldVarchar
/** @var string - Server name */
private string $server = '', 
    // TCMSFieldVarchar
/** @var string - Client IP address */
private string $ip = '', 
    // TCMSFieldVarchar
/** @var string - Extranet user login */
private string $dataExtranetUserName = ''  ) {}

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
public function getTimestamp(): string
{
    return $this->timestamp;
}
public function setTimestamp(string $timestamp): self
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


  
    // TCMSFieldVarchar
public function getLevel(): string
{
    return $this->level;
}
public function setLevel(string $level): self
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


  
    // TCMSFieldVarchar
public function getLine(): string
{
    return $this->line;
}
public function setLine(string $line): self
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


  
}
