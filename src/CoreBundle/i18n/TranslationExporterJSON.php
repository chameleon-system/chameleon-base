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

class TranslationExporterJSON implements TranslationDomainExportInterface
{
    /**
     * @var TranslatorBagInterface
     */
    private $translator;

    public function __construct(TranslatorBagInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string $locale
     * @param string $domain
     *
     * @return string
     */
    public function export($locale, $domain)
    {
        $messageArray = $this->getMessages($locale, $domain);

        return json_encode($messageArray);
    }

    /**
     * @param string $locale
     * @param string $domain
     *
     * @return array
     */
    private function getMessages($locale, $domain)
    {
        return $this->translator->getCatalogue($locale)->all($domain);
    }
}
