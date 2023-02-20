<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsTheme {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Preview image */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia = null,
/** @var null|string - Preview image */
private ?string $cmsMediaId = null
, 
    // TCMSFieldVarchar
/** @var string - Descriptive name */
private string $name = '', 
    // TCMSFieldText
/** @var string - Snippet chain */
private string $snippetChain = '', 
    // TCMSFieldVarchar
/** @var string - Own LESS file */
private string $lessFile = '', 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlockLayout[] - Theme block layouts */
private \Doctrine\Common\Collections\Collection $pkgCmsThemeBlockLayoutMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldLookupDirectory
/** @var string - Directory */
private string $directory = ''  ) {}

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
public function getSnippetChain(): string
{
    return $this->snippetChain;
}
public function setSnippetChain(string $snippetChain): self
{
    $this->snippetChain = $snippetChain;

    return $this;
}


  
    // TCMSFieldVarchar
public function getLessFile(): string
{
    return $this->lessFile;
}
public function setLessFile(string $lessFile): self
{
    $this->lessFile = $lessFile;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getPkgCmsThemeBlockLayoutMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgCmsThemeBlockLayoutMlt;
}
public function setPkgCmsThemeBlockLayoutMlt(\Doctrine\Common\Collections\Collection $pkgCmsThemeBlockLayoutMlt): self
{
    $this->pkgCmsThemeBlockLayoutMlt = $pkgCmsThemeBlockLayoutMlt;

    return $this;
}


  
    // TCMSFieldLookupDirectory
public function getDirectory(): string
{
    return $this->directory;
}
public function setDirectory(string $directory): self
{
    $this->directory = $directory;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsMedia(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->cmsMedia;
}
public function setCmsMedia(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $cmsMedia): self
{
    $this->cmsMedia = $cmsMedia;
    $this->cmsMediaId = $cmsMedia?->getId();

    return $this;
}
public function getCmsMediaId(): ?string
{
    return $this->cmsMediaId;
}
public function setCmsMediaId(?string $cmsMediaId): self
{
    $this->cmsMediaId = $cmsMediaId;
    // todo - load new id
    //$this->cmsMediaId = $?->getId();

    return $this;
}



  
}
