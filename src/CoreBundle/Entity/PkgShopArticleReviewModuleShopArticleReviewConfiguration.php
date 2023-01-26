<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopArticleReviewModuleShopArticleReviewConfiguration {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Only signed in users are allowed to write reviews  */
    public readonly bool $allowWriteReviewLoggedinUsersOnly, 
    /** Only signed in users are allowed to read reviews  */
    public readonly bool $allowShowReviewLoggedinUsersOnly, 
    /** Manage reviews */
    public readonly bool $manageReviews, 
    /** Reviews can be evaluated */
    public readonly bool $allowRateReview, 
    /** Customers can notify reviews */
    public readonly bool $allowReportReviews, 
    /** Customers can comment on reviews */
    public readonly bool $allowCommentReviews, 
    /** Number of evaluation credits */
    public readonly string $ratingCount, 
    /** Show number of reviews */
    public readonly string $countShowReviews, 
    /** Name of the author */
    public readonly string $optionShowAuthorName, 
    /** Heading */
    public readonly string $title, 
    /** Introduction */
    public readonly string $introText, 
    /** Closing text */
    public readonly string $outroText  ) {}
}