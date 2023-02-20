<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgCmsResultCache {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Owner identification */
private string $ownerHash = '', 
    // TCMSFieldVarchar
/** @var string - Identification */
private string $hash = ''  ) {}

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
public function getOwnerHash(): string
{
    return $this->ownerHash;
}
public function setOwnerHash(string $ownerHash): self
{
    $this->ownerHash = $ownerHash;

    return $this;
}


  
    // TCMSFieldVarchar
public function getHash(): string
{
    return $this->hash;
}
public function setHash(string $hash): self
{
    $this->hash = $hash;

    return $this;
}


  
}
