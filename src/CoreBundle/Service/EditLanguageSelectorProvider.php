<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

class EditLanguageSelectorProvider implements EditLanguageSelectorProviderInterface
{
    public function isEnabled(): bool
    {
        return false;
    }

    public function getSelectorData(): array
    {
        return [
            'active' => null,
            'items' => [],
        ];
    }

    public function getModuleViewData(array $selectorData): array
    {
        $activeItem = $selectorData['active'] ?? null;
        $currentLanguage = '';
        $activeSelectionKey = null;
        $activeLabel = null;

        if (null !== $activeItem) {
            $currentLanguage = strtoupper((string) $activeItem['iconIsoCode']);
            $activeSelectionKey = (string) $activeItem['selectionKey'];
            $activeLabel = (string) $activeItem['label'];
        }

        $editLanguageItems = [];
        $editLanguages = [];
        foreach ($selectorData['items'] as $item) {
            $editLanguageItems[] = [
                'selectionKey' => (string) $item['selectionKey'],
                'label' => (string) $item['label'],
                'iconIsoCode' => strtoupper((string) $item['iconIsoCode']),
            ];
            $editLanguages[(string) $item['selectionKey']] = (string) $item['label'];
        }

        return [
            'activeEditLanguageSelectionKey' => $activeSelectionKey,
            'activeEditLanguageLabel' => $activeLabel,
            'editLanguageItems' => $editLanguageItems,
            'editLanguageOptions' => '',
            'editLanguages' => $editLanguages,
            'activeEditLanguageIso' => $currentLanguage,
        ];
    }

    public function applySelection(string $selectionKey): void
    {
    }

    public function getRedirectParameterListAfterSelection(array $parameterList): array
    {
        return $parameterList;
    }
}
