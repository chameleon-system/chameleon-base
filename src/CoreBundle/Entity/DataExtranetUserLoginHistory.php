<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\DataExtranetUser;

class DataExtranetUserLoginHistory {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var DataExtranetUser|null - Corresponding user */
private ?DataExtranetUser $dataExtranetUser = null
, 
    // TCMSFieldVarchar
/** @var string - User IP */
private string $userIp = ''  ) {}

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
    // TCMSFieldLookup
public function getDataExtranetUser(): ?DataExtranetUser
{
    return $this->dataExtranetUser;
}

public function setDataExtranetUser(?DataExtranetUser $dataExtranetUser): self
{
    $this->dataExtranetUser = $dataExtranetUser;

    return $this;
}


  
    // TCMSFieldVarchar
public function getUserIp(): string
{
    return $this->userIp;
}
public function setUserIp(string $userIp): self
{
    $this->userIp = $userIp;

    return $this;
}


  
}
