<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ModuleList {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Title */
    public readonly string $name, 
    /** Sub headline */
    public readonly string $subHeadline, 
    /** Date */
    public readonly \DateTime $dateToday, 
    /** Category */
    public readonly \ChameleonSystem\CoreBundle\Entity\ModuleListCat $moduleListCatId, 
    /** Introduction */
    public readonly string $teaserText, 
    /** Description */
    public readonly string $description, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsDocument[] Document pool */
    public readonly array $dataPool, 
    /** Position */
    public readonly int $position  ) {}
}