<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\String_\RenameStringRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(RenameStringRector::class, [
        // Basis-Shop-Aufrufe:
        'TdbShop::GetInstance()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveShop()",
        'TShop::GetInstance()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveShop()",
        // Aufrufe mit Parametern – hier wird die öffnende Klammer ersetzt:
        'TdbShop::GetInstance('
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getShopForPortalId(",
        // Kategorie-Aufrufe:
        'TdbShop::GetActiveCategory()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveCategory()",
        'TShop::GetActiveCategory()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveCategory()",
        // Produkt-Aufrufe:
        'TdbShop::GetActiveItem()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveProduct()",
        'TShop::GetActiveItem()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveProduct()",
        // Manuelle Ersetzung für Fälle wie "hop->GetActiveItem()":
        'hop->GetActiveItem()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveProduct()",
        // Root-Kategorie-Aufrufe:
        'TdbShop::GetActiveRootCategory()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveRootCategory()",
        'TShop::GetActiveRootCategory()'
        => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('chameleon_system_shop.shop_service')->getActiveRootCategory()",
        // Allgemeiner Hinweis: Falls in der Codebasis auch Aufrufe mit "->GetActiveRootCategory()" vorkommen:
        '->GetActiveRootCategory()'
        => "->getActiveRootCategory()",
    ]);
};
