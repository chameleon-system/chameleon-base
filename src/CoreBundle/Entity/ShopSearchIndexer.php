<?php
namespace ChameleonSystem\CoreBundle\Entity;


class ShopSearchIndexer {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Number of lines to process */
private string $totalRowsToProcess = ''  ) {}

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
public function getTotalRowsToProcess(): string
{
    return $this->totalRowsToProcess;
}
public function setTotalRowsToProcess(string $totalRowsToProcess): self
{
    $this->totalRowsToProcess = $totalRowsToProcess;

    return $this;
}


  
}
