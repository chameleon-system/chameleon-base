<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCommentModuleConfig {
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
/** @var \ChameleonSystem\CoreBundle\Entity\PkgCommentType|null - Type of comment */
private \ChameleonSystem\CoreBundle\Entity\PkgCommentType|null $pkgCommentType = null,
/** @var null|string - Type of comment */
private ?string $pkgCommentTypeId = null
, 
    // TCMSFieldVarchar
/** @var string - Headline */
private string $name = '', 
    // TCMSFieldWYSIWYG
/** @var string - Introductory text */
private string $introText = '', 
    // TCMSFieldWYSIWYG
/** @var string - Closing text */
private string $closingText = '', 
    // TCMSFieldNumber
/** @var int - Comments per page */
private int $numberOfCommentsPerPage = 20, 
    // TCMSFieldBoolean
/** @var bool - Visible comments for guests */
private bool $guestCanSeeComments = true, 
    // TCMSFieldBoolean
/** @var bool - Comments from guests allowed */
private bool $guestCommentAllowed = false, 
    // TCMSFieldVarchar
/** @var string - Display if comment is deleted */
private string $commentOnDelete = 'Dieser Kommentar wurde gelöscht', 
    // TCMSFieldBoolean
/** @var bool - Show new comments first */
private bool $newestOnTop = true, 
    // TCMSFieldBoolean
/** @var bool - Use simple comment reporting function */
private bool $useSimpleReporting = false, 
    // TCMSFieldBoolean
/** @var bool - Show reported comments */
private bool $showReportedComments = true  ) {}

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


  
    // TCMSFieldLookup
public function getPkgCommentType(): \ChameleonSystem\CoreBundle\Entity\PkgCommentType|null
{
    return $this->pkgCommentType;
}
public function setPkgCommentType(\ChameleonSystem\CoreBundle\Entity\PkgCommentType|null $pkgCommentType): self
{
    $this->pkgCommentType = $pkgCommentType;
    $this->pkgCommentTypeId = $pkgCommentType?->getId();

    return $this;
}
public function getPkgCommentTypeId(): ?string
{
    return $this->pkgCommentTypeId;
}
public function setPkgCommentTypeId(?string $pkgCommentTypeId): self
{
    $this->pkgCommentTypeId = $pkgCommentTypeId;
    // todo - load new id
    //$this->pkgCommentTypeId = $?->getId();

    return $this;
}



  
    // TCMSFieldWYSIWYG
public function getIntroText(): string
{
    return $this->introText;
}
public function setIntroText(string $introText): self
{
    $this->introText = $introText;

    return $this;
}


  
    // TCMSFieldWYSIWYG
public function getClosingText(): string
{
    return $this->closingText;
}
public function setClosingText(string $closingText): self
{
    $this->closingText = $closingText;

    return $this;
}


  
    // TCMSFieldNumber
public function getNumberOfCommentsPerPage(): int
{
    return $this->numberOfCommentsPerPage;
}
public function setNumberOfCommentsPerPage(int $numberOfCommentsPerPage): self
{
    $this->numberOfCommentsPerPage = $numberOfCommentsPerPage;

    return $this;
}


  
    // TCMSFieldBoolean
public function isGuestCanSeeComments(): bool
{
    return $this->guestCanSeeComments;
}
public function setGuestCanSeeComments(bool $guestCanSeeComments): self
{
    $this->guestCanSeeComments = $guestCanSeeComments;

    return $this;
}


  
    // TCMSFieldBoolean
public function isGuestCommentAllowed(): bool
{
    return $this->guestCommentAllowed;
}
public function setGuestCommentAllowed(bool $guestCommentAllowed): self
{
    $this->guestCommentAllowed = $guestCommentAllowed;

    return $this;
}


  
    // TCMSFieldVarchar
public function getCommentOnDelete(): string
{
    return $this->commentOnDelete;
}
public function setCommentOnDelete(string $commentOnDelete): self
{
    $this->commentOnDelete = $commentOnDelete;

    return $this;
}


  
    // TCMSFieldBoolean
public function isNewestOnTop(): bool
{
    return $this->newestOnTop;
}
public function setNewestOnTop(bool $newestOnTop): self
{
    $this->newestOnTop = $newestOnTop;

    return $this;
}


  
    // TCMSFieldBoolean
public function isUseSimpleReporting(): bool
{
    return $this->useSimpleReporting;
}
public function setUseSimpleReporting(bool $useSimpleReporting): self
{
    $this->useSimpleReporting = $useSimpleReporting;

    return $this;
}


  
    // TCMSFieldBoolean
public function isShowReportedComments(): bool
{
    return $this->showReportedComments;
}
public function setShowReportedComments(bool $showReportedComments): self
{
    $this->showReportedComments = $showReportedComments;

    return $this;
}


  
}
