<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgShopRatingServiceRating {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Remote key */
private string $remoteKey = '', 
    // TCMSFieldVarchar
/** @var string - User who rates */
private string $ratingUser = ''  ) {}

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
public function getRemoteKey(): string
{
    return $this->remoteKey;
}
public function setRemoteKey(string $remoteKey): self
{
    $this->remoteKey = $remoteKey;

    return $this;
}


  
    // TCMSFieldVarchar
public function getRatingUser(): string
{
    return $this->ratingUser;
}
public function setRatingUser(string $ratingUser): self
{
    $this->ratingUser = $ratingUser;

    return $this;
}


  
}
