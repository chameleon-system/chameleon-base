<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleReview {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to product */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId, 
    /** Written by */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId, 
    /** Published */
    public readonly bool $publish, 
    /** Author */
    public readonly string $authorName, 
    /** Review title */
    public readonly string $title, 
    /** Author&#039;s email address */
    public readonly string $authorEmail, 
    /** Send comment notification to the author */
    public readonly bool $sendCommentNotification, 
    /** Rating */
    public readonly string $rating, 
    /** Helpful review */
    public readonly string $helpfulCount, 
    /** Review is not helpful */
    public readonly string $notHelpfulCount, 
    /** Action ID */
    public readonly string $actionId, 
    /** Review */
    public readonly string $comment, 
    /** Created on */
    public readonly \DateTime $datecreated, 
    /** IP address */
    public readonly string $userIp  ) {}
}