<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopWrappingCard {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Name */
    public readonly string $name, 
    /** Position */
    public readonly int $position, 
    /** Greeting card item */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId, 
    /** Suggested text */
    public readonly string $suggestedText  ) {}
}