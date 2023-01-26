<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSearchLog {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to shop */
    public readonly \ChameleonSystem\CoreBundle\Entity\Shop $shopId, 
    /** Language */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsLanguage $cmsLanguageId, 
    /** Search term */
    public readonly string $name, 
    /** Number of results */
    public readonly string $numberOfResults, 
    /** Search date */
    public readonly \DateTime $searchDate, 
    /** Executed by */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId  ) {}
}