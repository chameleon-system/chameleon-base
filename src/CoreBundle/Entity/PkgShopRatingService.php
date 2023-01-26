<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgShopRatingService {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Active */
    public readonly bool $active, 
    /** Name */
    public readonly string $name, 
    /** System name */
    public readonly string $systemName, 
    /** Ratings contain HTML */
    public readonly bool $ratingsContainHtml, 
    /** Icon */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $iconCmsMediaId, 
    /** Shop URL */
    public readonly string $shopUrl, 
    /** Rating URL */
    public readonly string $ratingUrl, 
    /** Rating API ID */
    public readonly string $ratingApiId, 
    /** Affiliate value */
    public readonly string $affiliateValue, 
    /** Email text */
    public readonly string $emailText, 
    /** Position */
    public readonly int $position, 
    /** Weighting */
    public readonly float $weight, 
    /** Frequency of use */
    public readonly string $numberOfTimesUsed, 
    /** Last used (calender week) */
    public readonly string $lastUsedYearWeek, 
    /** Allow import */
    public readonly bool $allowImport, 
    /** Allow sending of emails */
    public readonly bool $allowSendingEmails, 
    /** Current rating */
    public readonly float $currentRating, 
    /** Email provider */
    public readonly string $serviceEmail, 
    /** Current date of rating */
    public readonly \DateTime $currentRatingDate, 
    /** Class */
    public readonly string $class, 
    /** Subtype */
    public readonly string $classSubtype, 
    /** Class type */
    public readonly string $classType  ) {}
}