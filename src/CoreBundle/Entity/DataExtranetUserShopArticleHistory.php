<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataExtranetUserShopArticleHistory {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to customer */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId, 
    /** Article */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId, 
    /** Viewed on */
    public readonly \DateTime $datecreated  ) {}
}