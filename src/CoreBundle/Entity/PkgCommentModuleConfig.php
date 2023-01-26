<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCommentModuleConfig {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Headline */
    public readonly string $name, 
    /** Type of comment */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgCommentType $pkgCommentTypeId, 
    /** Introductory text */
    public readonly string $introText, 
    /** Closing text */
    public readonly string $closingText, 
    /** Comments per page */
    public readonly string $numberOfCommentsPerPage, 
    /** Visible comments for guests */
    public readonly bool $guestCanSeeComments, 
    /** Comments from guests allowed */
    public readonly bool $guestCommentAllowed, 
    /** Display if comment is deleted */
    public readonly string $commentOnDelete, 
    /** Show new comments first */
    public readonly bool $newestOnTop, 
    /** Use simple comment reporting function */
    public readonly bool $useSimpleReporting, 
    /** Show reported comments */
    public readonly bool $showReportedComments  ) {}
}