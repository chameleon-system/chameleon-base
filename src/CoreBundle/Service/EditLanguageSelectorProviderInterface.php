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

interface EditLanguageSelectorProviderInterface
{
    public function isEnabled(): bool;

    /**
     * @return array{
     *     active: array{selectionKey: string, label: string, iconIsoCode: string}|null,
     *     items: array<int, array{selectionKey: string, label: string, iconIsoCode: string}>
     * }
     */
    public function getSelectorData(): array;

    /**
     * @return array<string, mixed>
     */
    public function getModuleViewData(array $selectorData): array;

    public function applySelection(string $selectionKey): void;

    /**
     * @param array<string, mixed> $parameterList
     *
     * @return array<string, mixed>
     */
    public function getRedirectParameterListAfterSelection(array $parameterList): array;
}
