<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsExportProfilesFields {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to profile */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsExportProfiles $cmsExportProfilesId, 
    /** Field from table */
    public readonly string $fieldname, 
    /** Sort order */
    public readonly int $sortOrder, 
    /** HTML formatting */
    public readonly string $htmlTemplate  ) {}
}