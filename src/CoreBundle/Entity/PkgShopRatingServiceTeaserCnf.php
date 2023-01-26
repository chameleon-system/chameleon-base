<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopRatingServiceTeaserCnf {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Number of ratings to be selected */
    public readonly string $numberOfRatingsToSelectFrom, 
    /** Headline */
    public readonly string $headline, 
    /** Link name for &quot;show all&quot; */
    public readonly string $showAllLinkName, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgShopRatingService[] Rating service */
    public readonly array $pkgShopRatingServiceMlt  ) {}
}