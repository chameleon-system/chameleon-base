<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgNewsletterQueue {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgNewsletterUser|null - Newsletter subscriber */
private \ChameleonSystem\CoreBundle\Entity\PkgNewsletterUser|null $pkgNewsletterUser = null,
/** @var null|string - Newsletter subscriber */
private ?string $pkgNewsletterUserId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgNewsletterCampaign|null - Newsletter */
private \ChameleonSystem\CoreBundle\Entity\PkgNewsletterCampaign|null $pkgNewsletterCampaign = null,
/** @var null|string - Newsletter */
private ?string $pkgNewsletterCampaignId = null
, 
    // TCMSFieldDateTime
/** @var \DateTime|null - Shipped on */
private \DateTime|null $dateSent = null  ) {}

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
    // TCMSFieldLookup
public function getPkgNewsletterUser(): \ChameleonSystem\CoreBundle\Entity\PkgNewsletterUser|null
{
    return $this->pkgNewsletterUser;
}
public function setPkgNewsletterUser(\ChameleonSystem\CoreBundle\Entity\PkgNewsletterUser|null $pkgNewsletterUser): self
{
    $this->pkgNewsletterUser = $pkgNewsletterUser;
    $this->pkgNewsletterUserId = $pkgNewsletterUser?->getId();

    return $this;
}
public function getPkgNewsletterUserId(): ?string
{
    return $this->pkgNewsletterUserId;
}
public function setPkgNewsletterUserId(?string $pkgNewsletterUserId): self
{
    $this->pkgNewsletterUserId = $pkgNewsletterUserId;
    // todo - load new id
    //$this->pkgNewsletterUserId = $?->getId();

    return $this;
}



  
    // TCMSFieldDateTime
public function getDateSent(): \DateTime|null
{
    return $this->dateSent;
}
public function setDateSent(\DateTime|null $dateSent): self
{
    $this->dateSent = $dateSent;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgNewsletterCampaign(): \ChameleonSystem\CoreBundle\Entity\PkgNewsletterCampaign|null
{
    return $this->pkgNewsletterCampaign;
}
public function setPkgNewsletterCampaign(\ChameleonSystem\CoreBundle\Entity\PkgNewsletterCampaign|null $pkgNewsletterCampaign): self
{
    $this->pkgNewsletterCampaign = $pkgNewsletterCampaign;
    $this->pkgNewsletterCampaignId = $pkgNewsletterCampaign?->getId();

    return $this;
}
public function getPkgNewsletterCampaignId(): ?string
{
    return $this->pkgNewsletterCampaignId;
}
public function setPkgNewsletterCampaignId(?string $pkgNewsletterCampaignId): self
{
    $this->pkgNewsletterCampaignId = $pkgNewsletterCampaignId;
    // todo - load new id
    //$this->pkgNewsletterCampaignId = $?->getId();

    return $this;
}



  
}
