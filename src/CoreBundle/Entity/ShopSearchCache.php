<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSearchCache {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to shop */
    public readonly \ChameleonSystem\CoreBundle\Entity\Shop $shopId, 
    /** Search key */
    public readonly string $searchkey, 
    /** Last used */
    public readonly \DateTime $lastUsedDate, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopSearchCacheItem[] Results */
    public readonly array $shopSearchCacheItem, 
    /** Category hits */
    public readonly string $categoryHits, 
    /** Number of records found */
    public readonly string $numberOfRecordsFound  ) {}
}