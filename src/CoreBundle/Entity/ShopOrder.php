<?php
namespace ChameleonSystem\CoreBundle\Entity;

class ShopOrder {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Shop rating email - was processed */
    public readonly bool $pkgShopRatingServiceMailProcessed, 
    /** Belongs to shop */
    public readonly \ChameleonSystem\CoreBundle\Entity\Shop $shopId, 
    /** User has also subscribed to the newsletter */
    public readonly bool $newsletterSignup, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentIpnMessage[]  */
    public readonly array $pkgShopPaymentIpnMessage, 
    /** @var \ChameleonSystem\CoreBundle\Entity\AmazonPaymentIdMapping[] Amazon Pay */
    public readonly array $amazonPaymentIdMapping, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgShopPaymentTransaction[] Transactions */
    public readonly array $pkgShopPaymentTransaction, 
    /** Placed by portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** Order number */
    public readonly string $ordernumber, 
    /** Shop rating email - email was sent */
    public readonly bool $pkgShopRatingServiceMailSent, 
    /** Used rating service */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopRatingService $pkgShopRatingServiceId, 
    /** Basket ID (unique ID that is already assigned in the order process) */
    public readonly string $orderIdent, 
    /** Created on */
    public readonly \DateTime $datecreated, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderItem[] Items */
    public readonly array $shopOrderItem, 
    /** Shop customer */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId, 
    /** Customer number */
    public readonly string $customerNumber, 
    /** Customer email */
    public readonly string $userEmail, 
    /** Company */
    public readonly string $adrBillingCompany, 
    /** Salutation */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation $adrBillingSalutationId, 
    /** First name */
    public readonly string $adrBillingFirstname, 
    /** Last name */
    public readonly string $adrBillingLastname, 
    /** Address appendix */
    public readonly string $adrBillingAdditionalInfo, 
    /** Street */
    public readonly string $adrBillingStreet, 
    /** Street number */
    public readonly string $adrBillingStreetnr, 
    /** City */
    public readonly string $adrBillingCity, 
    /** Zip code */
    public readonly string $adrBillingPostalcode, 
    /** Country */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataCountry $adrBillingCountryId, 
    /** Telephone */
    public readonly string $adrBillingTelefon, 
    /** Fax */
    public readonly string $adrBillingFax, 
    /** Language */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsLanguage $cmsLanguageId, 
    /** User IP */
    public readonly string $userIp, 
    /** Ship to billing address */
    public readonly bool $adrShippingUseBilling, 
    /** Shipping address is a Packstation */
    public readonly bool $adrShippingIsDhlPackstation, 
    /** Company */
    public readonly string $adrShippingCompany, 
    /** Salutation */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetSalutation $adrShippingSalutationId, 
    /** First name */
    public readonly string $adrShippingFirstname, 
    /** Last name */
    public readonly string $adrShippingLastname, 
    /** Address appendix */
    public readonly string $adrShippingAdditionalInfo, 
    /** Street */
    public readonly string $adrShippingStreet, 
    /** Street number */
    public readonly string $adrShippingStreetnr, 
    /** City */
    public readonly string $adrShippingCity, 
    /** Zip code */
    public readonly string $adrShippingPostalcode, 
    /** Country */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataCountry $adrShippingCountryId, 
    /** Telephone */
    public readonly string $adrShippingTelefon, 
    /** Fax */
    public readonly string $adrShippingFax, 
    /** Shipping cost group */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopShippingGroup $shopShippingGroupId, 
    /** Shipping cost group – name */
    public readonly string $shopShippingGroupName, 
    /** Shipping cost group – costs */
    public readonly float $shopShippingGroupPrice, 
    /** Shipping cost group – tax rate */
    public readonly float $shopShippingGroupVatPercent, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderShippingGroupParameter[] Shipping cost group – parameter/user data */
    public readonly array $shopOrderShippingGroupParameter, 
    /** Payment method */
    public readonly \ChameleonSystem\CoreBundle\Entity\ShopPaymentMethod $shopPaymentMethodId, 
    /** Payment method – name */
    public readonly string $shopPaymentMethodName, 
    /** Payment method – costs */
    public readonly float $shopPaymentMethodPrice, 
    /** Payment method – tax rate */
    public readonly float $shopPaymentMethodVatPercent, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderPaymentMethodParameter[] Payment method – parameter/user data */
    public readonly array $shopOrderPaymentMethodParameter, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderVat[] Order VAT (by tax rate) */
    public readonly array $shopOrderVat, 
    /** Items value */
    public readonly float $valueArticle, 
    /** Total value */
    public readonly float $valueTotal, 
    /** Currency */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopCurrency $pkgShopCurrencyId, 
    /** Wrapping costs */
    public readonly float $valueWrapping, 
    /** Wrapping greeting card costs */
    public readonly float $valueWrappingCard, 
    /** Total voucher value */
    public readonly float $valueVouchers, 
    /** Value of the non-sponsered vouchers (discount vouchers) */
    public readonly float $valueVouchersNotSponsored, 
    /** Total discount value */
    public readonly float $valueDiscounts, 
    /** Total VAT value */
    public readonly float $valueVatTotal, 
    /** Total number of items */
    public readonly float $countArticles, 
    /** Number of different items */
    public readonly string $countUniqueArticles, 
    /** Total weight (grams) */
    public readonly float $totalweight, 
    /** Total volume (cubic meters) */
    public readonly float $totalvolume, 
    /** Saved order completely */
    public readonly bool $systemOrderSaveCompleted, 
    /** Order confirmation sent */
    public readonly bool $systemOrderNotificationSend, 
    /** Payment method executed successfully */
    public readonly bool $systemOrderPaymentMethodExecuted, 
    /** Payment method executed on */
    public readonly \DateTime $systemOrderPaymentMethodExecutedDate, 
    /** Paid */
    public readonly bool $orderIsPaid, 
    /** Marked as paid on */
    public readonly \DateTime $orderIsPaidDate, 
    /** Order was cancelled */
    public readonly bool $canceled, 
    /** Date the order was marked as cancelled */
    public readonly \DateTime $canceledDate, 
    /** Was exported for ERP on */
    public readonly \DateTime $systemOrderExportedDate, 
    /** Affiliate code */
    public readonly string $affiliateCode, 
    /** Order created via affiliate program */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgShopAffiliate $pkgShopAffiliateId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopVoucherUse[] Used vouchers */
    public readonly array $shopVoucherUse, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderDiscount[] Discount */
    public readonly array $shopOrderDiscount, 
    /** @var \ChameleonSystem\CoreBundle\Entity\ShopOrderStatus[] Order status */
    public readonly array $shopOrderStatus, 
    /** Mail object */
    public readonly string $objectMail, 
    /** Rating request sent on */
    public readonly \DateTime $pkgShopRatingServiceRatingProcessedOn, 
    /** VAT ID */
    public readonly string $vatId, 
    /** Internal comment */
    public readonly string $internalComment, 
    /** Date of shipment of all products */
    public readonly \DateTime $pkgShopRatingServiceOrderCompletelyShipped  ) {}
}