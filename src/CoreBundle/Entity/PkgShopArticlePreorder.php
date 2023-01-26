<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopArticlePreorder {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Preordered product */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId, 
    /** Email address */
    public readonly string $preorderUserEmail, 
    /** Date */
    public readonly \DateTime $preorderDate, 
    /** Belongs to portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId  ) {}
}