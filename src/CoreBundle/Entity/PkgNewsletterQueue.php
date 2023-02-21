<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgNewsletterUser;
use ChameleonSystem\CoreBundle\Entity\PkgNewsletterCampaign;

class PkgNewsletterQueue {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var PkgNewsletterUser|null - Newsletter subscriber */
private ?PkgNewsletterUser $pkgNewsletterU = null
, 
    // TCMSFieldLookup
/** @var PkgNewsletterCampaign|null - Newsletter */
private ?PkgNewsletterCampaign $pkgNewsletterCampaign = null
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
    // TCMSFieldLookup
public function getPkgNewsletterU(): ?PkgNewsletterUser
{
    return $this->pkgNewsletterU;
}

public function setPkgNewsletterU(?PkgNewsletterUser $pkgNewsletterU): self
{
    $this->pkgNewsletterU = $pkgNewsletterU;

    return $this;
}


  
    // TCMSFieldLookup
public function getPkgNewsletterCampaign(): ?PkgNewsletterCampaign
{
    return $this->pkgNewsletterCampaign;
}

public function setPkgNewsletterCampaign(?PkgNewsletterCampaign $pkgNewsletterCampaign): self
{
    $this->pkgNewsletterCampaign = $pkgNewsletterCampaign;

    return $this;
}


  
}
