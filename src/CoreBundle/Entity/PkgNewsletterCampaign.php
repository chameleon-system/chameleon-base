<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgNewsletterQueue;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class PkgNewsletterCampaign {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldVarchar
/** @var string - Campaign source (utm_source) */
private string $utmSource = '', 
    // TCMSFieldVarchar
/** @var string - Campaign medium (utm_medium) */
private string $utmMedium = 'email', 
    // TCMSFieldVarchar
/** @var string - Campaign content (utm_content) */
private string $utmContent = '', 
    // TCMSFieldVarchar
/** @var string - Campaign name (utm_campaign) */
private string $utmCampaign = '', 
    // TCMSFieldVarchar
/** @var string - Newsletter title */
private string $name = '', 
    // TCMSFieldVarchar
/** @var string - Subject */
private string $subject = '', 
    // TCMSFieldPropertyTable
/** @var Collection<int, pkgNewsletterQueue> - Queue items */
private Collection $pkgNewsletterQueueCollection = new ArrayCollection()
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
    // TCMSFieldVarchar
public function getUtmSource(): string
{
    return $this->utmSource;
}
public function setUtmSource(string $utmSource): self
{
    $this->utmSource = $utmSource;

    return $this;
}


  
    // TCMSFieldVarchar
public function getUtmMedium(): string
{
    return $this->utmMedium;
}
public function setUtmMedium(string $utmMedium): self
{
    $this->utmMedium = $utmMedium;

    return $this;
}


  
    // TCMSFieldVarchar
public function getUtmContent(): string
{
    return $this->utmContent;
}
public function setUtmContent(string $utmContent): self
{
    $this->utmContent = $utmContent;

    return $this;
}


  
    // TCMSFieldVarchar
public function getUtmCampaign(): string
{
    return $this->utmCampaign;
}
public function setUtmCampaign(string $utmCampaign): self
{
    $this->utmCampaign = $utmCampaign;

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
public function getSubject(): string
{
    return $this->subject;
}
public function setSubject(string $subject): self
{
    $this->subject = $subject;

    return $this;
}


  
    // TCMSFieldPropertyTable
/**
* @return Collection<int, pkgNewsletterQueue>
*/
public function getPkgNewsletterQueueCollection(): Collection
{
    return $this->pkgNewsletterQueueCollection;
}

public function addPkgNewsletterQueueCollection(pkgNewsletterQueue $pkgNewsletterQueue): self
{
    if (!$this->pkgNewsletterQueueCollection->contains($pkgNewsletterQueue)) {
        $this->pkgNewsletterQueueCollection->add($pkgNewsletterQueue);
        $pkgNewsletterQueue->setPkgNewsletterCampaign($this);
    }

    return $this;
}

public function removePkgNewsletterQueueCollection(pkgNewsletterQueue $pkgNewsletterQueue): self
{
    if ($this->pkgNewsletterQueueCollection->removeElement($pkgNewsletterQueue)) {
        // set the owning side to null (unless already changed)
        if ($pkgNewsletterQueue->getPkgNewsletterCampaign() === $this) {
            $pkgNewsletterQueue->setPkgNewsletterCampaign(null);
        }
    }

    return $this;
}


  
}
