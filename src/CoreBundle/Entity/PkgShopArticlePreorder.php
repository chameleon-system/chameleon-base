<?php
namespace ChameleonSystem\CoreBundle\Entity;


class PkgShopArticlePreorder {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Email address */
private string $preorderUserEmail = ''  ) {}

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
public function getPreorderUserEmail(): string
{
    return $this->preorderUserEmail;
}
public function setPreorderUserEmail(string $preorderUserEmail): self
{
    $this->preorderUserEmail = $preorderUserEmail;

    return $this;
}


  
}
