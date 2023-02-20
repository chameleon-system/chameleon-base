<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTplPageCmsMasterPagedefSpot;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class CmsTplPage {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldPropertyTable
/** @var Collection<int, cmsTplPageCmsMasterPagedefSpot> - Spots */
private Collection $cmsTplPageCmsMasterPagedefSpotCollection = new ArrayCollection()
, 
    // TCMSFieldVarchar
/** @var string - Page name */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - SEO pattern */
private string $seoPattern = '', 
    // TCMSFieldVarchar
/** @var string - IVW page code */
private string $ivwCode = '', 
    // TCMSFieldVarchar
/** @var string - Short description */
private string $metaDescription = '', 
    // TCMSFieldVarchar
/** @var string - Author */
private string $metaAuthor = '', 
    // TCMSFieldVarchar
/** @var string - Publisher */
private string $metaPublisher = '', 
    // TCMSFieldVarchar
/** @var string - Topic */
private string $metaPageTopic = '', 
    // TCMSFieldVarchar
/** @var string - Robots */
private string $metaRobots = 'index, follow', 
    // TCMSFieldVarchar
/** @var string - Revisit */
private string $metaRevisitAfter = ''  ) {}

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
    // TCMSFieldPropertyTable
/**
* @return Collection<int, cmsTplPageCmsMasterPagedefSpot>
*/
public function getCmsTplPageCmsMasterPagedefSpotCollection(): Collection
{
    return $this->cmsTplPageCmsMasterPagedefSpotCollection;
}

public function addCmsTplPageCmsMasterPagedefSpotCollection(cmsTplPageCmsMasterPagedefSpot $cmsTplPageCmsMasterPagedefSpot): self
{
    if (!$this->cmsTplPageCmsMasterPagedefSpotCollection->contains($cmsTplPageCmsMasterPagedefSpot)) {
        $this->cmsTplPageCmsMasterPagedefSpotCollection->add($cmsTplPageCmsMasterPagedefSpot);
        $cmsTplPageCmsMasterPagedefSpot->setCmsTplPage($this);
    }

    return $this;
}

public function removeCmsTplPageCmsMasterPagedefSpotCollection(cmsTplPageCmsMasterPagedefSpot $cmsTplPageCmsMasterPagedefSpot): self
{
    if ($this->cmsTplPageCmsMasterPagedefSpotCollection->removeElement($cmsTplPageCmsMasterPagedefSpot)) {
        // set the owning side to null (unless already changed)
        if ($cmsTplPageCmsMasterPagedefSpot->getCmsTplPage() === $this) {
            $cmsTplPageCmsMasterPagedefSpot->setCmsTplPage(null);
        }
    }

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


  
    // TCMSFieldVarchar
public function getSeoPattern(): string
{
    return $this->seoPattern;
}
public function setSeoPattern(string $seoPattern): self
{
    $this->seoPattern = $seoPattern;

    return $this;
}


  
    // TCMSFieldVarchar
public function getIvwCode(): string
{
    return $this->ivwCode;
}
public function setIvwCode(string $ivwCode): self
{
    $this->ivwCode = $ivwCode;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMetaDescription(): string
{
    return $this->metaDescription;
}
public function setMetaDescription(string $metaDescription): self
{
    $this->metaDescription = $metaDescription;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMetaAuthor(): string
{
    return $this->metaAuthor;
}
public function setMetaAuthor(string $metaAuthor): self
{
    $this->metaAuthor = $metaAuthor;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMetaPublisher(): string
{
    return $this->metaPublisher;
}
public function setMetaPublisher(string $metaPublisher): self
{
    $this->metaPublisher = $metaPublisher;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMetaPageTopic(): string
{
    return $this->metaPageTopic;
}
public function setMetaPageTopic(string $metaPageTopic): self
{
    $this->metaPageTopic = $metaPageTopic;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMetaRobots(): string
{
    return $this->metaRobots;
}
public function setMetaRobots(string $metaRobots): self
{
    $this->metaRobots = $metaRobots;

    return $this;
}


  
    // TCMSFieldVarchar
public function getMetaRevisitAfter(): string
{
    return $this->metaRevisitAfter;
}
public function setMetaRevisitAfter(string $metaRevisitAfter): self
{
    $this->metaRevisitAfter = $metaRevisitAfter;

    return $this;
}


  
}
