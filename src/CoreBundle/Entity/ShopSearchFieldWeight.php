<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopSearchFieldWeight {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to shop */
    public readonly \ChameleonSystem\CoreBundle\Entity\Shop $shopId, 
    /** Language */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsLanguage $cmsLanguageId, 
    /** Descriptive name of the field / table combination */
    public readonly string $name, 
    /** Table */
    public readonly string $tablename, 
    /** Field */
    public readonly string $fieldname, 
    /** Weight */
    public readonly float $weight, 
    /** Selection to be used */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopSearchQuery $shopSearchQueryId, 
    /** Field name in query */
    public readonly string $fieldNameInQuery, 
    /** Indexing partial words */
    public readonly bool $indexPartialWords  ) {}
}