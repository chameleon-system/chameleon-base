<?php
namespace ChameleonSystem\CoreBundle\Entity;

class Shop {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderStatusCode[] Available shipping status codes */
    public readonly array $shopOrderStatusCode, 
    /** Default currency */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopCurrency $defaultPkgShopCurrencyId, 
    /** Shop name */
    public readonly string $name, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] Belongs to these portals */
    public readonly array $cmsPortalMlt, 
    /** Shop main category */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopCategory $shopCategoryId, 
    /** Company name */
    public readonly string $adrCompany, 
    /** Company street */
    public readonly string $adrStreet, 
    /** Company zip code */
    public readonly string $adrZip, 
    /** Company city */
    public readonly string $adrCity, 
    /** Company country */
    public readonly \ChameleonSystem\CoreBundle\Entity\TCountry $tCountryId, 
    /** Telephone (customer service) */
    public readonly string $customerServiceTelephone, 
    /** Email (customer service) */
    public readonly string $customerServiceEmail, 
    /** VAT registration number */
    public readonly string $shopvatnumber, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopBankAccount[] Bank accounts */
    public readonly array $shopBankAccount, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUser[] Customers */
    public readonly array $dataExtranetUser, 
    /** Length of product history of an user */
    public readonly string $dataExtranetUserShopArticleHistoryMaxArticleCount, 
    /** Default sorting of items in the category view */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopModuleArticlelistOrderby $shopModuleArticlelistOrderbyId, 
    /** Default VAT group */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopVat $shopVatId, 
    /** Default shipping group */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopShippingGroup $shopShippingGroupId, 
    /** Make VAT of shipping costs dependent on basket contents */
    public readonly bool $shippingVatDependsOnBasketContents, 
    /** Default salutation */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation $dataExtranetSalutationId, 
    /** Default country */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataCountry $dataCountryId, 
    /** Affiliate URL parameter */
    public readonly string $affiliateParameterName, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgShopAffiliate[] Affiliate programs */
    public readonly array $pkgShopAffiliate, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleImageSize[] Size of product images */
    public readonly array $shopArticleImageSize, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopSystemInfo[] Shop specific information / text blocks (e.g. Terms and Conditions) */
    public readonly array $shopSystemInfo, 
    /** Replacement image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $notFoundImage, 
    /** Weight bonus for whole words in search */
    public readonly float $shopSearchWordBonus, 
    /** Weight of search word length */
    public readonly float $shopSearchWordLengthFactor, 
    /** Deduction for words that only sound similar */
    public readonly float $shopSearchSoundexPenalty, 
    /** Shortest searchable partial word */
    public readonly string $shopSearchMinIndexLength, 
    /** Longest searchable partial word */
    public readonly string $shopSearchMaxIndexLength, 
    /** Connect search items with AND */
    public readonly bool $shopSearchUseBooleanAnd, 
    /** Maximum age of search cache */
    public readonly string $maxSearchCacheAgeInHours, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopSearchLog[] Search log */
    public readonly array $shopSearchLog, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopSearchFieldWeight[] Fields weight */
    public readonly array $shopSearchFieldWeight, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopSearchIgnoreWord[] Words to be ignored in searches */
    public readonly array $shopSearchIgnoreWord, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopSearchKeywordArticle[] Manually selected search results */
    public readonly array $shopSearchKeywordArticle, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopSearchCache[] Search cache */
    public readonly array $shopSearchCache, 
    /** Name of the spot in the layouts containing the basket module */
    public readonly string $basketSpotName, 
    /** Name of the spot containing the central shop handler */
    public readonly string $shopCentralHandlerSpotName, 
    /** Show empty categories in shop */
    public readonly bool $showEmptyCategories, 
    /** Variant parents can be purchased */
    public readonly bool $allowPurchaseOfVariantParents, 
    /** Load inactive variants */
    public readonly bool $loadInactiveVariants, 
    /** Synchronize profile address with billing address */
    public readonly bool $syncProfileDataWithBillingData, 
    /** Is the user allowed to have more than one billing address? */
    public readonly bool $allowMultipleBillingAddresses, 
    /** Is the user allowed to have more than one shipping address? */
    public readonly bool $allowMultipleShippingAddresses, 
    /** Allow guest orders? */
    public readonly bool $allowGuestPurchase, 
    /** Archive customers product recommendations */
    public readonly bool $logArticleSuggestions, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopStockMessage[] Stock messages */
    public readonly array $shopStockMessage, 
    /** Export key */
    public readonly string $exportKey, 
    /** Basket info text */
    public readonly string $cartInfoText, 
    /** Results list filter */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter $pkgShopListfilterPostsearchId, 
    /** If there are no results, refer to page &quot;no results for product search&quot; */
    public readonly bool $redirectToNotFoundPageProductSearchOnNoResults, 
    /** Turn on search log */
    public readonly bool $useShopSearchLog, 
    /** Category list filter for categories without subcategories */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopListfilter $pkgShopListfilterCategoryFilterId, 
    /** Maximum size of cookie for item history (in KB) */
    public readonly string $dataExtranetUserShopArticleHistoryMaxCookieSize, 
    /** Use SEO-URLs for products */
    public readonly string $productUrlMode, 
    /** Shipping delay (days) */
    public readonly string $shopreviewmailMailDelay, 
    /** Recipients (percent) */
    public readonly float $shopreviewmailPercentOfCustomers, 
    /** For each order */
    public readonly bool $shopreviewmailSendForEachOrder, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgShopFooterCategory[] Footer categories */
    public readonly array $pkgShopFooterCategory  ) {}
}