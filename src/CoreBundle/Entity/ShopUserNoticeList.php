<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopUserNoticeList {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to customer */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId, 
    /** Added */
    public readonly \DateTime $dateAdded, 
    /** Article */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId, 
    /** Units */
    public readonly float $amount  ) {}
}