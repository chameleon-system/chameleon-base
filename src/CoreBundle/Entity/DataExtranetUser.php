<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataExtranetUser {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to shop */
    public readonly \ChameleonSystem\CoreBundle\Entity\Shop $shopId, 
    /** Belongs to portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** Customer number */
    public readonly string $customerNumber, 
    /** Login */
    public readonly string $name, 
    /** Password */
    public readonly string $password, 
    /** Password change key */
    public readonly string $passwordChangeKey, 
    /** Date of the request to change password */
    public readonly \DateTime $passwordChangeTimeStamp, 
    /** Name */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation $dataExtranetSalutationId, 
    /** First name */
    public readonly string $firstname, 
    /** Last name */
    public readonly string $lastname, 
    /** Company */
    public readonly string $company, 
    /** Street */
    public readonly string $street, 
    /** Street Number */
    public readonly string $streetnr, 
    /** Zip code */
    public readonly string $postalcode, 
    /** City */
    public readonly string $city, 
    /** Country */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataCountry $dataCountryId, 
    /** USTID */
    public readonly string $vatId, 
    /** Telephone */
    public readonly string $telefon, 
    /** Mobile */
    public readonly string $mobile, 
    /** Address appendix */
    public readonly string $addressAdditionalInfo, 
    /** Alias */
    public readonly string $aliasName, 
    /** Email */
    public readonly string $email, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] Customer groups */
    public readonly array $dataExtranetGroupMlt, 
    /** Fax */
    public readonly string $fax, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUserAddress[] Addresses */
    public readonly array $dataExtranetUserAddress, 
    /** Session key */
    public readonly string $sessionKey, 
    /** Login timestamp */
    public readonly string $loginTimestamp, 
    /** Login salt */
    public readonly string $loginSalt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUserLoginHistory[] Login process */
    public readonly array $dataExtranetUserLoginHistory, 
    /** Last billing address */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUserAddress $defaultBillingAddressId, 
    /** Last used shipping address */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUserAddress $defaultShippingAddressId, 
    /** Date of subscription */
    public readonly \DateTime $datecreated, 
    /** Confirmed */
    public readonly bool $confirmed, 
    /** Confirmation key */
    public readonly string $tmpconfirmkey, 
    /** Confirmed on */
    public readonly \DateTime $confirmedon, 
    /** Registration email sent */
    public readonly bool $regEmailSend, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopUserPurchasedVoucher[] Bought vouchers */
    public readonly array $shopUserPurchasedVoucher, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopUserNoticeList[] Notice list */
    public readonly array $shopUserNoticeList, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopOrder[] Orders */
    public readonly array $shopOrder, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetUserShopArticleHistory[] Last viewed */
    public readonly array $dataExtranetUserShopArticleHistory, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopSearchLog[] Searches executed by customer */
    public readonly array $shopSearchLog, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopSuggestArticleLog[] Customer recommendations */
    public readonly array $shopSuggestArticleLog, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopArticleReview[] Reviews */
    public readonly array $shopArticleReview, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgShopWishlist[] Wish list */
    public readonly array $pkgShopWishlist, 
    /** Date of birth */
    public readonly \DateTime $birthdate, 
    /** Currency */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopCurrency $pkgShopCurrencyId  ) {}
}