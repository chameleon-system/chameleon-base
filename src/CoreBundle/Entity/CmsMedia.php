<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsMedia {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Height */
    public readonly string $height, 
    /** Image type */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsFiletype $cmsFiletypeId, 
    /** File size */
    public readonly string $filesize, 
    /** Image category */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $cmsMediaTreeId, 
    /** Width */
    public readonly string $width, 
    /** Title / Description */
    public readonly string $description, 
    /** Keywords / Tags */
    public readonly string $metatags, 
    /** Supported file types */
    public readonly string $filetypes, 
    /** Alt tag */
    public readonly string $altTag, 
    /** Systemname */
    public readonly string $systemname, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsTags[] Tags */
    public readonly array $cmsTagsMlt, 
    /** Custom file name */
    public readonly string $customFilename, 
    /** Path */
    public readonly string $path, 
    /** Preview image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaId, 
    /** Video HTML code */
    public readonly string $externalEmbedCode, 
    /** Thumbnail of an external video */
    public readonly string $externalVideoThumbnail, 
    /** Last changed on */
    public readonly \DateTime $timeStamp, 
    /** Last changed */
    public readonly \DateTime $dateChanged, 
    /** Refresh Token */
    public readonly string $refreshToken, 
    /** Last changed by */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsUser $cmsUserId, 
    /** Video ID with external host */
    public readonly string $externalVideoId  ) {}
}