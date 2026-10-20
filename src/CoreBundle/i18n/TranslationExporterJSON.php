<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\i18n;

use ChameleonSystem\CoreBundle\i18n\Interfaces\TranslationDomainExportInterface;
use Symfony\Component\Translation\TranslatorBagInterface;

readonly class TranslationExporterJSON implements TranslationDomainExportInterface
{
    public function __construct(
        private TranslatorBagInterface $translator
    ) {
    }

    public function export(string $locale, string $domain): false|string
    {
        $messageArray = $this->getMessages($locale, $domain);

        return json_encode($messageArray);
    }

    private function getMessages(string $locale, string $domain): array
    {
        $catalogue = $this->translator->getCatalogue($locale);
        $messages = [];
        while (null !== $catalogue) {
            $messages += $catalogue->all($domain);
            $catalogue = $catalogue->getFallbackCatalogue();
        }

        return $messages;
    }
}
