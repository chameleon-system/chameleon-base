<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\i18n\Controller;

use ChameleonSystem\CoreBundle\i18n\Interfaces\ActiveCmsUserPermissionInterface;
use ChameleonSystem\CoreBundle\i18n\Interfaces\TranslationDomainExportInterface;
use ChameleonSystem\CoreBundle\i18n\TranslationConstants;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class JsTranslationController
{
    /**
     * @var TranslationDomainExportInterface
     */
    private $exporter;
    /**
     * @var ActiveCmsUserPermissionInterface
     */
    private $activeUserPermission;

    public function __construct(
        TranslationDomainExportInterface $exporter,
        ActiveCmsUserPermissionInterface $activeUserPermission
    ) {
        $this->exporter = $exporter;
        $this->activeUserPermission = $activeUserPermission;
    }

    /**
     * @param string $_locale
     *
     * @return Response
     */
    public function __invoke($_locale)
    {
        if (false === $this->activeUserPermission->hasPermissionToExportTranslationDatabase()) {
            throw new AccessDeniedException('chameleon_system_core.translation_exporter.error_not_logged_in');
        }
        $exportString = $this->exporter->export($_locale, TranslationConstants::DOMAIN_BACKEND_JS);

        $jsResponse = <<<JS
if ( typeof CHAMELEON === "undefined" || !CHAMELEON ) { var CHAMELEON = {}; }
CHAMELEON.CORE = CHAMELEON.CORE || {};
CHAMELEON.CORE.i18n = CHAMELEON.CORE.i18n || {};
CHAMELEON.CORE.i18n.isInitialized = true;
CHAMELEON.CORE.i18n.Translation = {$exportString};
JS;

        return new Response($jsResponse, Response::HTTP_OK, ['Content-Type' => 'application/javascript']);
    }
}
