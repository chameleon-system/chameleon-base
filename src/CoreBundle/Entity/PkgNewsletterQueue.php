<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgNewsletterCampaign;

class PkgNewsletterQueue {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
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
    // TCMSFieldLookupParentID
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
