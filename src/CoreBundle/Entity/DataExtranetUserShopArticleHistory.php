<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\DataExtranetUser;

class DataExtranetUserShopArticleHistory {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var DataExtranetUser|null - Belongs to customer */
private ?DataExtranetUser $dataExtranetUser = null
  ) {}

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
    // TCMSFieldLookupParentID
public function getDataExtranetUser(): ?DataExtranetUser
{
    return $this->dataExtranetUser;
}

public function setDataExtranetUser(?DataExtranetUser $dataExtranetUser): self
{
    $this->dataExtranetUser = $dataExtranetUser;

    return $this;
}


  
}
