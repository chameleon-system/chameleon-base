<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSearchQuery {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Name / title of query */
private string $name = '', 
    // TCMSFieldText
/** @var string - Query */
private string $query = '', 
    // TCMSFieldBoolean
/** @var bool - Index is running */
private bool $indexRunning = false, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Index started on */
private \DateTime|null $indexStarted = null, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Index completed on */
private \DateTime|null $indexCompleted = null  ) {}

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


  
    // TCMSFieldText
public function getQuery(): string
{
    return $this->query;
}
public function setQuery(string $query): self
{
    $this->query = $query;

    return $this;
}


  
    // TCMSFieldBoolean
public function isIndexRunning(): bool
{
    return $this->indexRunning;
}
public function setIndexRunning(bool $indexRunning): self
{
    $this->indexRunning = $indexRunning;

    return $this;
}


  
    // TCMSFieldDateTime
public function getIndexStarted(): \DateTime|null
{
    return $this->indexStarted;
}
public function setIndexStarted(\DateTime|null $indexStarted): self
{
    $this->indexStarted = $indexStarted;

    return $this;
}


  
    // TCMSFieldDateTime
public function getIndexCompleted(): \DateTime|null
{
    return $this->indexCompleted;
}
public function setIndexCompleted(\DateTime|null $indexCompleted): self
{
    $this->indexCompleted = $indexCompleted;

    return $this;
}


  
}
