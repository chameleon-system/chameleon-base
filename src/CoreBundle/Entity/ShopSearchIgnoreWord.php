<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSearchIgnoreWord {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to shop */
    public readonly \ChameleonSystem\CoreBundle\Entity\Shop $shopId, 
    /** Language */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsLanguage $cmsLanguageId, 
    /** Word */
    public readonly string $name  ) {}
}