<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsMasterPagedef;
use ChameleonSystem\CoreBundle\Entity\CmsTplPageCmsMasterPagedefSpot;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ChameleonSystem\CoreBundle\Entity\CmsPortal;
use ChameleonSystem\CoreBundle\Entity\CmsMedia;
use ChameleonSystem\CoreBundle\Entity\CmsUser;
use ChameleonSystem\CoreBundle\Entity\CmsLanguage;

class CmsTplPage {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var CmsMasterPagedef|null - Page template */
private ?CmsMasterPagedef $cmsMasterPagedef = null
, 
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
    // TCMSFieldLookup
/** @var CmsPortal|null - Belongs to portal / website */
private ?CmsPortal $cmsPortal = null
, 
    // TCMSFieldLookup
/** @var CmsMedia|null - Background image */
private ?CmsMedia $backgroundIm = null
, 
    // TCMSFieldLookup
/** @var CmsUser|null - Created by */
private ?CmsUser $cmsUser = null
, 
    // TCMSFieldVarchar
/** @var string - IVW page code */
private string $ivwCode = '', 
    // TCMSFieldLookup
/** @var CmsLanguage|null - Content language */
private ?CmsLanguage $cmsLanguage = null
, 
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
    // TCMSFieldLookup
public function getCmsMasterPagedef(): ?CmsMasterPagedef
{
    return $this->cmsMasterPagedef;
}

public function setCmsMasterPagedef(?CmsMasterPagedef $cmsMasterPagedef): self
{
    $this->cmsMasterPagedef = $cmsMasterPagedef;

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


  
    // TCMSFieldLookup
public function getCmsPortal(): ?CmsPortal
{
    return $this->cmsPortal;
}

public function setCmsPortal(?CmsPortal $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;

    return $this;
}


  
    // TCMSFieldLookup
public function getBackgroundIm(): ?CmsMedia
{
    return $this->backgroundIm;
}

public function setBackgroundIm(?CmsMedia $backgroundIm): self
{
    $this->backgroundIm = $backgroundIm;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsUser(): ?CmsUser
{
    return $this->cmsUser;
}

public function setCmsUser(?CmsUser $cmsUser): self
{
    $this->cmsUser = $cmsUser;

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


  
    // TCMSFieldLookup
public function getCmsLanguage(): ?CmsLanguage
{
    return $this->cmsLanguage;
}

public function setCmsLanguage(?CmsLanguage $cmsLanguage): self
{
    $this->cmsLanguage = $cmsLanguage;

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
