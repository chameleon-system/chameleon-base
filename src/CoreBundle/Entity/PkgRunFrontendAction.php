<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgRunFrontendAction {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null -  */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string -  */
private ?string $cmsPortalId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null - Language */
private \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null $cmsLanguage = null,
/** @var null|string - Language */
private ?string $cmsLanguageId = null
, 
    // TCMSFieldVarchar
/** @var string - Class */
private string $class = '', 
    // TCMSFieldVarchar
/** @var string -  */
private string $randomKey = '', 
    // TCMSFieldDateTime
/** @var \DateTime|null - Expiry date */
private \DateTime|null $expireDate = null, 
    // TCMSFieldText
/** @var string -  */
private string $parameter = ''  ) {}

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
public function getClass(): string
{
    return $this->class;
}
public function setClass(string $class): self
{
    $this->class = $class;

    return $this;
}


  
    // TCMSFieldVarchar
public function getRandomKey(): string
{
    return $this->randomKey;
}
public function setRandomKey(string $randomKey): self
{
    $this->randomKey = $randomKey;

    return $this;
}


  
    // TCMSFieldDateTime
public function getExpireDate(): \DateTime|null
{
    return $this->expireDate;
}
public function setExpireDate(\DateTime|null $expireDate): self
{
    $this->expireDate = $expireDate;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsPortal(): \ChameleonSystem\CoreBundle\Entity\CmsPortal|null
{
    return $this->cmsPortal;
}
public function setCmsPortal(\ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;
    $this->cmsPortalId = $cmsPortal?->getId();

    return $this;
}
public function getCmsPortalId(): ?string
{
    return $this->cmsPortalId;
}
public function setCmsPortalId(?string $cmsPortalId): self
{
    $this->cmsPortalId = $cmsPortalId;
    // todo - load new id
    //$this->cmsPortalId = $?->getId();

    return $this;
}



  
    // TCMSFieldText
public function getParameter(): string
{
    return $this->parameter;
}
public function setParameter(string $parameter): self
{
    $this->parameter = $parameter;

    return $this;
}


  
    // TCMSFieldLookup
public function getCmsLanguage(): \ChameleonSystem\CoreBundle\Entity\CmsLanguage|null
{
    return $this->cmsLanguage;
}
public function setCmsLanguage(\ChameleonSystem\CoreBundle\Entity\CmsLanguage|null $cmsLanguage): self
{
    $this->cmsLanguage = $cmsLanguage;
    $this->cmsLanguageId = $cmsLanguage?->getId();

    return $this;
}
public function getCmsLanguageId(): ?string
{
    return $this->cmsLanguageId;
}
public function setCmsLanguageId(?string $cmsLanguageId): self
{
    $this->cmsLanguageId = $cmsLanguageId;
    // todo - load new id
    //$this->cmsLanguageId = $?->getId();

    return $this;
}



  
}
