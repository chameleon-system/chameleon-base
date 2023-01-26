<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleStock {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId, 
    /** Amount */
    public readonly string $amount  ) {}
}