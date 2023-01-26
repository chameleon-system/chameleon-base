<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopRatingServiceRating {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Rating service */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopRatingService $pkgShopRatingServiceId, 
    /** Remote key */
    public readonly string $remoteKey, 
    /** Rating */
    public readonly float $score, 
    /** Raw data */
    public readonly string $rawdata, 
    /** User who rates */
    public readonly string $ratingUser, 
    /** Rating text */
    public readonly string $ratingText, 
    /** Date of rating */
    public readonly \DateTime $ratingDate  ) {}
}