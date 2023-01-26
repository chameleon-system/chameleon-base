<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSuggestArticleLog {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Created on */
    public readonly \DateTime $datecreated, 
    /** Shop customer */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId, 
    /** Product / item */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId, 
    /** From (email) */
    public readonly string $fromEmail, 
    /** From (name) */
    public readonly string $fromName, 
    /** Feedback recipient (email address) */
    public readonly string $toEmail, 
    /** To (name) */
    public readonly string $toName, 
    /** Comment */
    public readonly string $comment  ) {}
}