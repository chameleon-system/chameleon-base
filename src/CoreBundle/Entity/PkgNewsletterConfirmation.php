<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgNewsletterConfirmation {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Double opt-out key */
private string $optoutKey = ''  ) {}

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
