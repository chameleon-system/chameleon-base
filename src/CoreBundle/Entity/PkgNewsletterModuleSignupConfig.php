<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance;

class PkgNewsletterModuleSignupConfig {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var CmsTplModuleInstance|null - Belongs to module instance */
private ?CmsTplModuleInstance $cmsTplModuleInstance = null
, 
    // TCMSFieldVarchar
/** @var string - Signup (title) */
private string $signupHeadline = '', 
    // TCMSFieldVarchar
/** @var string - Confirmation (title) */
private string $confirmTitle = '', 
    // TCMSFieldVarchar
/** @var string - Successful subscription (title) */
private string $signedupHeadline = '', 
    // TCMSFieldVarchar
/** @var string - Signup not possible anymore (title) */
private string $nonewsignupTitle = ''  ) {}

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
public function getCmsTplModuleInstance(): ?CmsTplModuleInstance
{
    return $this->cmsTplModuleInstance;
}

public function setCmsTplModuleInstance(?CmsTplModuleInstance $cmsTplModuleInstance): self
{
    $this->cmsTplModuleInstance = $cmsTplModuleInstance;

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


  
}
