<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgNewsletterRobinson {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Email address */
private string $email = '', 
    // TCMSFieldVarchar
/** @var string - Reason for blacklisting */
private string $reason = ''  ) {}

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
public function getEmail(): string
{
    return $this->email;
}
public function setEmail(string $email): self
{
    $this->email = $email;

    return $this;
}


  
    // TCMSFieldVarchar
public function getReason(): string
{
    return $this->reason;
}
public function setReason(string $reason): self
{
    $this->reason = $reason;

    return $this;
}


  
}
