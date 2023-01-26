<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgComment {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Comment type */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgCommentType $pkgCommentTypeId, 
    /** Object ID */
    public readonly string $itemId, 
    /** User */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId, 
    /** Creation date */
    public readonly \DateTime $createdTimestamp, 
    /** Comment text */
    public readonly string $comment, 
    /** Comment feedback */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgComment $pkgCommentId, 
    /** Comment has been deleted */
    public readonly bool $markAsDeleted, 
    /** Comment has been reported */
    public readonly bool $markAsReported  ) {}
}