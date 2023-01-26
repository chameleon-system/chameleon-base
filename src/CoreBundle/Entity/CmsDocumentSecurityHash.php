<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsDocumentSecurityHash {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /**  */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsDocument $cmsDocumentId, 
    /**  */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId, 
    /**  */
    public readonly \DateTime $publishdate, 
    /**  */
    public readonly \DateTime $enddate, 
    /**  */
    public readonly string $token  ) {}
}