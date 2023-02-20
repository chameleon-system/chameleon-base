<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgShopWishlistOrderItem {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Email of the wishlist owner */
private string $dataExtranetUserEmail = ''  ) {}

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
public function getDataExtranetUserEmail(): string
{
    return $this->dataExtranetUserEmail;
}
public function setDataExtranetUserEmail(string $dataExtranetUserEmail): self
{
    $this->dataExtranetUserEmail = $dataExtranetUserEmail;

    return $this;
}


  
}
