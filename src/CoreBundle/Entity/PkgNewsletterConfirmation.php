<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgNewsletterConfirmation {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgNewsletterGroup|null - Subscription to newsletter group */
private \ChameleonSystem\CoreBundle\Entity\PkgNewsletterGroup|null $pkgNewsletterGroup = null,
/** @var null|string - Subscription to newsletter group */
private ?string $pkgNewsletterGroupId = null
, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Registration date */
private \DateTime|null $registrationDate = null, 
    // TCMSFieldBoolean
/** @var bool - Registration confirmed */
private bool $confirmation = false, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Registration confirmed on */
private \DateTime|null $confirmationDate = null, 
    // TCMSFieldVarchar
/** @var string - Double opt-out key */
private string $optoutKey = ''  ) {}

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
    // TCMSFieldDateTime
public function getRegistrationDate(): \DateTime|null
{
    return $this->registrationDate;
}
public function setRegistrationDate(\DateTime|null $registrationDate): self
{
    $this->registrationDate = $registrationDate;

    return $this;
}


  
    // TCMSFieldBoolean
public function isConfirmation(): bool
{
    return $this->confirmation;
}
public function setConfirmation(bool $confirmation): self
{
    $this->confirmation = $confirmation;

    return $this;
}


  
    // TCMSFieldDateTime
public function getConfirmationDate(): \DateTime|null
{
    return $this->confirmationDate;
}
public function setConfirmationDate(\DateTime|null $confirmationDate): self
{
    $this->confirmationDate = $confirmationDate;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgNewsletterGroup(): \ChameleonSystem\CoreBundle\Entity\PkgNewsletterGroup|null
{
    return $this->pkgNewsletterGroup;
}
public function setPkgNewsletterGroup(\ChameleonSystem\CoreBundle\Entity\PkgNewsletterGroup|null $pkgNewsletterGroup): self
{
    $this->pkgNewsletterGroup = $pkgNewsletterGroup;
    $this->pkgNewsletterGroupId = $pkgNewsletterGroup?->getId();

    return $this;
}
public function getPkgNewsletterGroupId(): ?string
{
    return $this->pkgNewsletterGroupId;
}
public function setPkgNewsletterGroupId(?string $pkgNewsletterGroupId): self
{
    $this->pkgNewsletterGroupId = $pkgNewsletterGroupId;
    // todo - load new id
    //$this->pkgNewsletterGroupId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getOptoutKey(): string
{
    return $this->optoutKey;
}
public function setOptoutKey(string $optoutKey): self
{
    $this->optoutKey = $optoutKey;

    return $this;
}


  
}
