<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMigrationCounter {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name */
private string $name = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMigrationFile[] - Update data */
private \Doctrine\Common\Collections\Collection $cmsMigrationFileCollection = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
    // TCMSFieldVarchar
public function getName(): string
{
    return $this->name;
}
public function setName(string $name): self
{
    $this->name = $name;

    return $this;
}


  
    // TCMSFieldPropertyTable
public function getCmsMigrationFileCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->cmsMigrationFileCollection;
}
public function setCmsMigrationFileCollection(\Doctrine\Common\Collections\Collection $cmsMigrationFileCollection): self
{
    $this->cmsMigrationFileCollection = $cmsMigrationFileCollection;

    return $this;
}


  
}
