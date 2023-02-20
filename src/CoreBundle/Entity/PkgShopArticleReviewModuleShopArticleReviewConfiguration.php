<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopArticleReviewModuleShopArticleReviewConfiguration {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null - Belongs to module */
private \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance|null $cmsTplModuleInstance = null,
/** @var null|string - Belongs to module */
private ?string $cmsTplModuleInstanceId = null
, 
    // TCMSFieldBoolean
/** @var bool - Only signed in users are allowed to write reviews  */
private bool $allowWriteReviewLoggedinUsersOnly = true, 
    // TCMSFieldBoolean
/** @var bool - Only signed in users are allowed to read reviews  */
private bool $allowShowReviewLoggedinUsersOnly = false, 
    // TCMSFieldBoolean
/** @var bool - Manage reviews */
private bool $manageReviews = false, 
    // TCMSFieldBoolean
/** @var bool - Reviews can be evaluated */
private bool $allowRateReview = false, 
    // TCMSFieldBoolean
/** @var bool - Customers can notify reviews */
private bool $allowReportReviews = false, 
    // TCMSFieldBoolean
/** @var bool - Customers can comment on reviews */
private bool $allowCommentReviews = false, 
    // TCMSFieldNumber
/** @var int - Number of evaluation credits */
private int $ratingCount = 5, 
    // TCMSFieldNumber
/** @var int - Show number of reviews */
private int $countShowReviews = 3, 
    // TCMSFieldOption
/** @var string - Name of the author */
private string $optionShowAuthorName = 'full_name', 
    // TCMSFieldVarchar
/** @var string - Heading */
private string $title = '', 
    // TCMSFieldWYSIWYG
/** @var string - Introduction */
private string $introText = '', 
    // TCMSFieldWYSIWYG
/** @var string - Closing text */
private string $outroText = ''  ) {}

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



  
    // TCMSFieldBoolean
public function isAllowWriteReviewLoggedinUsersOnly(): bool
{
    return $this->allowWriteReviewLoggedinUsersOnly;
}
public function setAllowWriteReviewLoggedinUsersOnly(bool $allowWriteReviewLoggedinUsersOnly): self
{
    $this->allowWriteReviewLoggedinUsersOnly = $allowWriteReviewLoggedinUsersOnly;

    return $this;
}


  
    // TCMSFieldBoolean
public function isAllowShowReviewLoggedinUsersOnly(): bool
{
    return $this->allowShowReviewLoggedinUsersOnly;
}
public function setAllowShowReviewLoggedinUsersOnly(bool $allowShowReviewLoggedinUsersOnly): self
{
    $this->allowShowReviewLoggedinUsersOnly = $allowShowReviewLoggedinUsersOnly;

    return $this;
}


  
    // TCMSFieldBoolean
public function isManageReviews(): bool
{
    return $this->manageReviews;
}
public function setManageReviews(bool $manageReviews): self
{
    $this->manageReviews = $manageReviews;

    return $this;
}


  
    // TCMSFieldBoolean
public function isAllowRateReview(): bool
{
    return $this->allowRateReview;
}
public function setAllowRateReview(bool $allowRateReview): self
{
    $this->allowRateReview = $allowRateReview;

    return $this;
}


  
    // TCMSFieldBoolean
public function isAllowReportReviews(): bool
{
    return $this->allowReportReviews;
}
public function setAllowReportReviews(bool $allowReportReviews): self
{
    $this->allowReportReviews = $allowReportReviews;

    return $this;
}


  
    // TCMSFieldBoolean
public function isAllowCommentReviews(): bool
{
    return $this->allowCommentReviews;
}
public function setAllowCommentReviews(bool $allowCommentReviews): self
{
    $this->allowCommentReviews = $allowCommentReviews;

    return $this;
}


  
    // TCMSFieldNumber
public function getRatingCount(): int
{
    return $this->ratingCount;
}
public function setRatingCount(int $ratingCount): self
{
    $this->ratingCount = $ratingCount;

    return $this;
}


  
    // TCMSFieldNumber
public function getCountShowReviews(): int
{
    return $this->countShowReviews;
}
public function setCountShowReviews(int $countShowReviews): self
{
    $this->countShowReviews = $countShowReviews;

    return $this;
}


  
    // TCMSFieldOption
public function getOptionShowAuthorName(): string
{
    return $this->optionShowAuthorName;
}
public function setOptionShowAuthorName(string $optionShowAuthorName): self
{
    $this->optionShowAuthorName = $optionShowAuthorName;

    return $this;
}


  
    // TCMSFieldVarchar
public function getTitle(): string
{
    return $this->title;
}
public function setTitle(string $title): self
{
    $this->title = $title;

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
public function getOutroText(): string
{
    return $this->outroText;
}
public function setOutroText(string $outroText): self
{
    $this->outroText = $outroText;

    return $this;
}


  
}
