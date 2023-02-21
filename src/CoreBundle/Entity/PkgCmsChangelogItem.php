<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\PkgCmsChangelogSet;
use ChameleonSystem\CoreBundle\Entity\CmsFieldConf;

class PkgCmsChangelogItem {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookup
/** @var PkgCmsChangelogSet|null - Changeset */
private ?PkgCmsChangelogSet $pkgCmsChangelogSet = null
, 
    // TCMSFieldLookup
/** @var CmsFieldConf|null - Changed field */
private ?CmsFieldConf $cmsFieldC = null
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
public function getPkgCmsChangelogSet(): ?PkgCmsChangelogSet
{
    return $this->pkgCmsChangelogSet;
}

public function setPkgCmsChangelogSet(?PkgCmsChangelogSet $pkgCmsChangelogSet): self
{
    $this->pkgCmsChangelogSet = $pkgCmsChangelogSet;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsFieldC(): ?CmsFieldConf
{
    return $this->cmsFieldC;
}

public function setCmsFieldC(?CmsFieldConf $cmsFieldC): self
{
    $this->cmsFieldC = $cmsFieldC;

    return $this;
}


  
}
