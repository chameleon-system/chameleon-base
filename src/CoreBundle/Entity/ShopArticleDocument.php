<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopArticleDocument {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to article */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticle $shopArticleId, 
    /** Article document type */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopArticleDocumentType $shopArticleDocumentTypeId, 
    /** Document */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsDocument $cmsDocumentId, 
    /** Position */
    public readonly int $position  ) {}
}