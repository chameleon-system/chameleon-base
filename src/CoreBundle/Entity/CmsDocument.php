<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsDocument {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Properties */
    public readonly string $cmsFiletypeId, 
    /** Name */
    public readonly string $name, 
    /** File name */
    public readonly string $filename, 
    /** Description */
    public readonly string $description, 
    /** Private */
    public readonly bool $private, 
    /** Time-limited download authorization */
    public readonly bool $tokenProtected, 
    /** Last changed on */
    public readonly \DateTime $timeStamp, 
    /** Last changed by */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsUser $cmsUserId, 
    /** Image width */
    public readonly string $hiddenImageWidth, 
    /** Image height */
    public readonly string $hiddenImageHeight, 
    /** User downloads */
    public readonly string $counter, 
    /** Folder */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsDocumentTree $cmsDocumentTreeId, 
    /** File size */
    public readonly string $filesize, 
    /** SEO Name */
    public readonly string $seoName  ) {}
}