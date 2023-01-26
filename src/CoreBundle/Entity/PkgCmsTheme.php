<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsTheme {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Descriptive name */
    public readonly string $name, 
    /** Snippet chain */
    public readonly string $snippetChain, 
    /** Own LESS file */
    public readonly string $lessFile, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgCmsThemeBlockLayout[] Theme block layouts */
    public readonly array $pkgCmsThemeBlockLayoutMlt, 
    /** Directory */
    public readonly string $directory, 
    /** Preview image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaId  ) {}
}