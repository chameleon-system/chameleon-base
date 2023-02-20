<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgNewsletterGroup {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - From (name) */
private string $fromName = '', 
    // TCMSFieldVarchar
/** @var string - Reply email address */
private string $replyEmail = '', 
    // TCMSFieldVarchar
/** @var string - Name of the recipient list */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - From (email address) */
private string $fromEmail = ''  ) {}

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
public function getFromName(): string
{
    return $this->fromName;
}
public function setFromName(string $fromName): self
{
    $this->fromName = $fromName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getReplyEmail(): string
{
    return $this->replyEmail;
}
public function setReplyEmail(string $replyEmail): self
{
    $this->replyEmail = $replyEmail;

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
public function getFromEmail(): string
{
    return $this->fromEmail;
}
public function setFromEmail(string $fromEmail): self
{
    $this->fromEmail = $fromEmail;

    return $this;
}


  
}
