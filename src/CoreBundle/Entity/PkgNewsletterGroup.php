<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgNewsletterGroup {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsMedia|null - Logo header image of newsletter */
private \ChameleonSystem\CoreBundle\Entity\CmsMedia|null $logoHeader = null,
/** @var null|string - Logo header image of newsletter */
private ?string $logoHeaderId = null
,   
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Portal */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Portal */
private ?string $cmsPortalId = null
, 
    // TCMSFieldVarchar
/** @var string - From (name) */
private string $fromName = '', 
    // TCMSFieldEmail
/** @var string - Reply email address */
private string $replyEmail = '', 
    // TCMSFieldVarchar
/** @var string - Name of the recipient list */
private string $name = '', 
    // TCMSFieldEmail
/** @var string - From (email address) */
private string $fromEmail = '', 
    // TCMSFieldWYSIWYG
/** @var string - Imprint */
private string $imprint = '', 
    // TCMSFieldBoolean
/** @var bool - Send to all newsletter users */
private bool $includeAllNewsletterUsers = false, 
    // TCMSFieldBoolean
/** @var bool - Newsletter users without assignment to a newsletter group */
private bool $includeNewsletterUserNotAssignedToAnyGroup = false, 
    // TCMSFieldBoolean
/** @var bool - Include all newsletter users WITHOUT extranet account in the list */
private bool $includeAllNewsletterUsersWithNoExtranetAccount = false, 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] - Send to users with following extranet groups */
private \Doctrine\Common\Collections\Collection $dataExtranetGroupMlt = new \Doctrine\Common\Collections\ArrayCollection()  ) {}

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
public function getLogoHeader(): \ChameleonSystem\CoreBundle\Entity\CmsMedia|null
{
    return $this->logoHeader;
}
public function setLogoHeader(\ChameleonSystem\CoreBundle\Entity\CmsMedia|null $logoHeader): self
{
    $this->logoHeader = $logoHeader;
    $this->logoHeaderId = $logoHeader?->getId();

    return $this;
}
public function getLogoHeaderId(): ?string
{
    return $this->logoHeaderId;
}
public function setLogoHeaderId(?string $logoHeaderId): self
{
    $this->logoHeaderId = $logoHeaderId;
    // todo - load new id
    //$this->logoHeaderId = $?->getId();

    return $this;
}



  
    // TCMSFieldVarchar
public function getFromName(): string
{
    return $this->fromName;
}
public function setFromName(string $fromName): self
{
    $this->fromName = $fromName;

    return $this;
}


  
    // TCMSFieldEmail
public function getReplyEmail(): string
{
    return $this->replyEmail;
}
public function setReplyEmail(string $replyEmail): self
{
    $this->replyEmail = $replyEmail;

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


  
    // TCMSFieldEmail
public function getFromEmail(): string
{
    return $this->fromEmail;
}
public function setFromEmail(string $fromEmail): self
{
    $this->fromEmail = $fromEmail;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getImprint(): string
{
    return $this->imprint;
}
public function setImprint(string $imprint): self
{
    $this->imprint = $imprint;

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



  
    // TCMSFieldBoolean
public function isIncludeAllNewsletterUsers(): bool
{
    return $this->includeAllNewsletterUsers;
}
public function setIncludeAllNewsletterUsers(bool $includeAllNewsletterUsers): self
{
    $this->includeAllNewsletterUsers = $includeAllNewsletterUsers;

    return $this;
}


  
    // TCMSFieldBoolean
public function isIncludeNewsletterUserNotAssignedToAnyGroup(): bool
{
    return $this->includeNewsletterUserNotAssignedToAnyGroup;
}
public function setIncludeNewsletterUserNotAssignedToAnyGroup(bool $includeNewsletterUserNotAssignedToAnyGroup): self
{
    $this->includeNewsletterUserNotAssignedToAnyGroup = $includeNewsletterUserNotAssignedToAnyGroup;

    return $this;
}


  
    // TCMSFieldBoolean
public function isIncludeAllNewsletterUsersWithNoExtranetAccount(): bool
{
    return $this->includeAllNewsletterUsersWithNoExtranetAccount;
}
public function setIncludeAllNewsletterUsersWithNoExtranetAccount(bool $includeAllNewsletterUsersWithNoExtranetAccount): self
{
    $this->includeAllNewsletterUsersWithNoExtranetAccount = $includeAllNewsletterUsersWithNoExtranetAccount;

    return $this;
}


  
    // TCMSFieldLookupMultiselect
public function getDataExtranetGroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->dataExtranetGroupMlt;
}
public function setDataExtranetGroupMlt(\Doctrine\Common\Collections\Collection $dataExtranetGroupMlt): self
{
    $this->dataExtranetGroupMlt = $dataExtranetGroupMlt;

    return $this;
}


  
}
