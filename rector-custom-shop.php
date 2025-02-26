<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\String_\RenameStringRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(RenameStringRector::class, [
        // Basic shop calls:
        'TdbShop::GetInstance()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveShop()",
        'TShop::GetInstance()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveShop()",
        // Calls with parameters – here the opening parenthesis is replaced:
        'TdbShop::GetInstance('
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getShopForPortalId(",
        // Category calls:
        'TdbShop::GetActiveCategory()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveCategory()",
        'TShop::GetActiveCategory()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveCategory()",
        // Product calls:
        'TdbShop::GetActiveItem()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveProduct()",
        'TShop::GetActiveItem()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveProduct()",
        // Manual replacement for cases like "hop->GetActiveItem()":
        'hop->GetActiveItem()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveProduct()",
        // Root category calls:
        'TdbShop::GetActiveRootCategory()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveRootCategory()",
        'TShop::GetActiveRootCategory()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveRootCategory()",
        // General note: If calls like "->GetActiveRootCategory()" appear in the codebase:
        '->GetActiveRootCategory()'
        => "->getActiveRootCategory()",
    ]);
};