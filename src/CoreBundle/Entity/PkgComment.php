<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgComment {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Object ID */
private string $itemId = ''  ) {}

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
public function getItemId(): string
{
    return $this->itemId;
}
public function setItemId(string $itemId): self
{
    $this->itemId = $itemId;

    return $this;
}


  
}
