<?php
namespace ChameleonSystem\CoreBundle\Entity;


class ModuleFeedback {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Headline */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Feedback recipient (email address) */
private string $toEmail = '', 
    // TCMSFieldVarchar
/** @var string - Sender (email address) */
private string $fromEmail = '', 
    // TCMSFieldVarchar
/** @var string - Default subject */
private string $defaultSubject = ''  ) {}

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
public function getToEmail(): string
{
    return $this->toEmail;
}
public function setToEmail(string $toEmail): self
{
    $this->toEmail = $toEmail;

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


  
    // TCMSFieldVarchar
public function getDefaultSubject(): string
{
    return $this->defaultSubject;
}
public function setDefaultSubject(string $defaultSubject): self
{
    $this->defaultSubject = $defaultSubject;

    return $this;
}


  
}
