<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTplPage {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Navigation path image for searches */
    public readonly string $treePathSearchString, 
    /** Page template */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMasterPagedef $cmsMasterPagedefId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsTplPageCmsMasterPagedefSpot[] Spots */
    public readonly array $cmsTplPageCmsMasterPagedefSpot, 
    /** Page name */
    public readonly string $name, 
    /** SEO pattern */
    public readonly string $seoPattern, 
    /** Belongs to portal / website */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** Primary navigation tree node */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTree $primaryTreeIdHidden, 
    /** @var array&lt;int,string&gt; Page image */
    public readonly array $images, 
    /** Background image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $backgroundImage, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsUsergroup[] Additional authorized groups */
    public readonly array $cmsUsergroupMlt, 
    /** Created by */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsUser $cmsUserId, 
    /** Use SSL */
    public readonly bool $usessl, 
    /** Restrict access */
    public readonly bool $extranetPage, 
    /** Enable access for non-confirmed users */
    public readonly bool $accessNotConfirmedUser, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] Restrict to the following extranet groups */
    public readonly array $dataExtranetGroupMlt, 
    /** IVW page code */
    public readonly string $ivwCode, 
    /** Content language */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsLanguage $cmsLanguageId, 
    /** Short description */
    public readonly string $metaDescription, 
    /** Search terms */
    public readonly string $metaKeywords, 
    /** Keyword language */
    public readonly string $metaKeywordLanguage, 
    /** Author */
    public readonly string $metaAuthor, 
    /** Publisher */
    public readonly string $metaPublisher, 
    /** Topic */
    public readonly string $metaPageTopic, 
    /** Cacheable (pragma) */
    public readonly string $metaPragma, 
    /** Robots */
    public readonly string $metaRobots, 
    /** Revisit */
    public readonly string $metaRevisitAfter  ) {}
}