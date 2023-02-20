<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgShopRatingServiceHistory {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - List of rating services */
private string $pkgShopRatingServiceIdList = ''  ) {}

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
public function getPkgShopRatingServiceIdList(): string
{
    return $this->pkgShopRatingServiceIdList;
}
public function setPkgShopRatingServiceIdList(string $pkgShopRatingServiceIdList): self
{
    $this->pkgShopRatingServiceIdList = $pkgShopRatingServiceIdList;

    return $this;
}


  
}
