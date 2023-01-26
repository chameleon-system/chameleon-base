<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleContributor {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to article */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId, 
    /** Contributing person */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopContributor $shopContributorId, 
    /** Role of the contributing person / contribution type */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopContributorType $shopContributorTypeId, 
    /** Position */
    public readonly int $position  ) {}
}