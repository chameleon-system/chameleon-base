<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgNewsletterModuleSignupConfig {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null - Belongs to module instance */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance = null,
/** @var null|string - Belongs to module instance */
private ?string $cmsTplModuleInstanceId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null - Belongs to newsletter module */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $mainModuleInstance = null,
/** @var null|string - Belongs to newsletter module */
private ?string $mainModuleInstanceId = null
, 
    // TCMSFieldLookupMultiselectCheckboxes
/** @var \ChameleonSystem\CoreBundle\Entity\PkgNewsletterGroup[] - Subscription possible for */
private \Doctrine\Common\Collections\Collection $pkgNewsletterGroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldBoolean
/** @var bool - Use double opt-in */
private bool $useDoubleoptin = true, 
    // TCMSFieldVarchar
/** @var string - Signup (title) */
private string $signupHeadline = '', 
    // TCMSFieldWYSIWYG
/** @var string - Signup  (text) */
private string $signupText = '', 
    // TCMSFieldVarchar
/** @var string - Confirmation (title) */
private string $confirmTitle = '', 
    // TCMSFieldWYSIWYG
/** @var string - Confirmation (text) */
private string $confirmText = '', 
    // TCMSFieldVarchar
/** @var string - Successful subscription (title) */
private string $signedupHeadline = '', 
    // TCMSFieldWYSIWYG
/** @var string - Successful subscription (text) */
private string $signedupText = '', 
    // TCMSFieldVarchar
/** @var string - Signup not possible anymore (title) */
private string $nonewsignupTitle = '', 
    // TCMSFieldWYSIWYG
/** @var string - Signup not possible anymore (text) */
private string $nonewsignupText = ''  ) {}

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
public function getCmsTplModuleInstance(): \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null
{
    return $this->cmsTplModuleInstance;
}
public function setCmsTplModuleInstance(\ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance): self
{
    $this->cmsTplModuleInstance = $cmsTplModuleInstance;
    $this->cmsTplModuleInstanceId = $cmsTplModuleInstance?->getId();

    return $this;
}
public function getCmsTplModuleInstanceId(): ?string
{
    return $this->cmsTplModuleInstanceId;
}
public function setCmsTplModuleInstanceId(?string $cmsTplModuleInstanceId): self
{
    $this->cmsTplModuleInstanceId = $cmsTplModuleInstanceId;
    // todo - load new id
    //$this->cmsTplModuleInstanceId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookup
public function getMainModuleInstance(): \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null
{
    return $this->mainModuleInstance;
}
public function setMainModuleInstance(\ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $mainModuleInstance): self
{
    $this->mainModuleInstance = $mainModuleInstance;
    $this->mainModuleInstanceId = $mainModuleInstance?->getId();

    return $this;
}
public function getMainModuleInstanceId(): ?string
{
    return $this->mainModuleInstanceId;
}
public function setMainModuleInstanceId(?string $mainModuleInstanceId): self
{
    $this->mainModuleInstanceId = $mainModuleInstanceId;
    // todo - load new id
    //$this->mainModuleInstanceId = $?->getId();

    return $this;
}



  
    // TCMSFieldLookupMultiselectCheckboxes
public function getPkgNewsletterGroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgNewsletterGroupMlt;
}
public function setPkgNewsletterGroupMlt(\Doctrine\Common\Collections\Collection $pkgNewsletterGroupMlt): self
{
    $this->pkgNewsletterGroupMlt = $pkgNewsletterGroupMlt;

    return $this;
}


  
    // TCMSFieldBoolean
public function isUseDoubleoptin(): bool
{
    return $this->useDoubleoptin;
}
public function setUseDoubleoptin(bool $useDoubleoptin): self
{
    $this->useDoubleoptin = $useDoubleoptin;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSignupHeadline(): string
{
    return $this->signupHeadline;
}
public function setSignupHeadline(string $signupHeadline): self
{
    $this->signupHeadline = $signupHeadline;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getSignupText(): string
{
    return $this->signupText;
}
public function setSignupText(string $signupText): self
{
    $this->signupText = $signupText;

    return $this;
}


  
    // TCMSFieldVarchar
public function getConfirmTitle(): string
{
    return $this->confirmTitle;
}
public function setConfirmTitle(string $confirmTitle): self
{
    $this->confirmTitle = $confirmTitle;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getConfirmText(): string
{
    return $this->confirmText;
}
public function setConfirmText(string $confirmText): self
{
    $this->confirmText = $confirmText;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSignedupHeadline(): string
{
    return $this->signedupHeadline;
}
public function setSignedupHeadline(string $signedupHeadline): self
{
    $this->signedupHeadline = $signedupHeadline;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getSignedupText(): string
{
    return $this->signedupText;
}
public function setSignedupText(string $signedupText): self
{
    $this->signedupText = $signedupText;

    return $this;
}


  
    // TCMSFieldVarchar
public function getNonewsignupTitle(): string
{
    return $this->nonewsignupTitle;
}
public function setNonewsignupTitle(string $nonewsignupTitle): self
{
    $this->nonewsignupTitle = $nonewsignupTitle;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getNonewsignupText(): string
{
    return $this->nonewsignupText;
}
public function setNonewsignupText(string $nonewsignupText): self
{
    $this->nonewsignupText = $nonewsignupText;

    return $this;
}


  
}
