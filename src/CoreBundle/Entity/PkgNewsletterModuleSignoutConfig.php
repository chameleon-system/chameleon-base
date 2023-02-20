<?php
namespace ChameleonSystem\CoreBundle\Entity;

use ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance;

class PkgNewsletterModuleSignoutConfig {
  public function __construct(
    private string $id,
    private int|null $cmsident = null,
        
    // TCMSFieldLookupParentID
/** @var CmsTplModuleInstance|null - Belongs to module instance */
private ?CmsTplModuleInstance $cmsTplModuleInstance = null
, 
    // TCMSFieldVarchar
/** @var string - Signout (title) */
private string $signoutTitle = '', 
    // TCMSFieldVarchar
/** @var string - Signout confirmation (title) */
private string $signoutConfirmTitle = '', 
    // TCMSFieldVarchar
/** @var string - Signed out (title) */
private string $signedoutTitle = '', 
    // TCMSFieldVarchar
/** @var string - No newsletter signed up for (title) */
private string $noNewsletterSignedup = ''  ) {}

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
public function getSignoutTitle(): string
{
    return $this->signoutTitle;
}
public function setSignoutTitle(string $signoutTitle): self
{
    $this->signoutTitle = $signoutTitle;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSignoutConfirmTitle(): string
{
    return $this->signoutConfirmTitle;
}
public function setSignoutConfirmTitle(string $signoutConfirmTitle): self
{
    $this->signoutConfirmTitle = $signoutConfirmTitle;

    return $this;
}


  
    // TCMSFieldVarchar
public function getSignedoutTitle(): string
{
    return $this->signedoutTitle;
}
public function setSignedoutTitle(string $signedoutTitle): self
{
    $this->signedoutTitle = $signedoutTitle;

    return $this;
}


  
    // TCMSFieldVarchar
public function getNoNewsletterSignedup(): string
{
    return $this->noNewsletterSignedup;
}
public function setNoNewsletterSignedup(string $noNewsletterSignedup): self
{
    $this->noNewsletterSignedup = $noNewsletterSignedup;

    return $this;
}


  
}
