<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsChangelogItem {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\PkgCmsChangelogSet|null - Changeset */
private \ChameleonSystem\CoreBundle\Entity\PkgCmsChangelogSet|null $pkgCmsChangelogSet = null,
/** @var null|string - Changeset */
private ?string $pkgCmsChangelogSetId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsFieldConf|null - Changed field */
private \ChameleonSystem\CoreBundle\Entity\CmsFieldConf|null $cmsFieldConf = null,
/** @var null|string - Changed field */
private ?string $cmsFieldConfId = null
, 
    // TCMSFieldText
/** @var string - Old value */
private string $valueOld = '', 
    // TCMSFieldText
/** @var string - New value */
private string $valueNew = ''  ) {}

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
public function getPkgCmsChangelogSet(): \ChameleonSystem\CoreBundle\Entity\PkgCmsChangelogSet|null
{
    return $this->pkgCmsChangelogSet;
}
public function setPkgCmsChangelogSet(\ChameleonSystem\CoreBundle\Entity\PkgCmsChangelogSet|null $pkgCmsChangelogSet): self
{
    $this->pkgCmsChangelogSet = $pkgCmsChangelogSet;
    $this->pkgCmsChangelogSetId = $pkgCmsChangelogSet?->getId();

    return $this;
}
public function getPkgCmsChangelogSetId(): ?string
{
    return $this->pkgCmsChangelogSetId;
}
public function setPkgCmsChangelogSetId(?string $pkgCmsChangelogSetId): self
{
    $this->pkgCmsChangelogSetId = $pkgCmsChangelogSetId;
    // todo - load new id
    //$this->pkgCmsChangelogSetId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getCmsFieldConf(): \ChameleonSystem\CoreBundle\Entity\CmsFieldConf|null
{
    return $this->cmsFieldConf;
}
public function setCmsFieldConf(\ChameleonSystem\CoreBundle\Entity\CmsFieldConf|null $cmsFieldConf): self
{
    $this->cmsFieldConf = $cmsFieldConf;
    $this->cmsFieldConfId = $cmsFieldConf?->getId();

    return $this;
}
public function getCmsFieldConfId(): ?string
{
    return $this->cmsFieldConfId;
}
public function setCmsFieldConfId(?string $cmsFieldConfId): self
{
    $this->cmsFieldConfId = $cmsFieldConfId;
    // todo - load new id
    //$this->cmsFieldConfId = $?->getId();

    return $this;
}



  
    // TCMSFieldText
public function getValueOld(): string
{
    return $this->valueOld;
}
public function setValueOld(string $valueOld): self
{
    $this->valueOld = $valueOld;

    return $this;
}


  
    // TCMSFieldText
public function getValueNew(): string
{
    return $this->valueNew;
}
public function setValueNew(string $valueNew): self
{
    $this->valueNew = $valueNew;

    return $this;
}


  
}
