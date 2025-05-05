<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\UniversalUploader\Bridge\JqueryFileUpload;

use ChameleonSystem\CoreBundle\UniversalUploader\Interfaces\UploaderPluginIntegrationServiceInterface;

class JqueryFileUploadIntegrationService implements UploaderPluginIntegrationServiceInterface
{
    /**
     * @return array
     */
    public function getHtmlHeadIncludes()
    {
        $includes = [];

        $includes[] = '<link href="'.\TGlobal::GetStaticURLToWebLib('/components/jqueryFileUpload/css/jquery.fileupload.css').'" rel="stylesheet" type="text/css" />';
        $includes[] = '<link href="'.\TGlobal::GetStaticURLToWebLib('/universalUploader/jqueryFileUpload/uploader.css').'" rel="stylesheet" type="text/css" />';
        $includes[] = '<script src="'.\TGlobal::GetStaticURLToWebLib('/javascript/jquery-ui-1.12.1.custom/jquery-ui.js').'" type="text/javascript"></script>';

        // The Iframe Transport is required for browsers without support for XHR file uploads
        $includes[] = '<script src="'.\TGlobal::GetStaticURLToWebLib('/components/jqueryFileUpload/js/jquery.iframe-transport.js').'" type="text/javascript"></script>';

        // The File Upload library
        $includes[] = '<script src="'.\TGlobal::GetStaticURLToWebLib('/components/jqueryFileUpload/js/jquery.fileupload.js').'" type="text/javascript"></script>';

        // The File Upload processing plugin
        $includes[] = '<script src="'.\TGlobal::GetStaticURLToWebLib('/components/jqueryFileUpload/js/jquery.fileupload-process.js').'" type="text/javascript"></script>';

        // The File Upload validator plugin
        $includes[] = '<script src="'.\TGlobal::GetStaticURLToWebLib('/components/jqueryFileUpload/js/jquery.fileupload-validate.js').'" type="text/javascript"></script>';

        // Chameleon plugin
        $includes[] = '<script src="'.\TGlobal::GetStaticURLToWebLib('/universalUploader/jqueryFileUpload/jquery.fileupload-chameleon.js').'" type="text/javascript"></script>';

        // Chameleon integration plugin
        $includes[] = '<script src="'.\TGlobal::GetStaticURLToWebLib('/universalUploader/jqueryFileUpload/uploader.js').'" type="text/javascript"></script>';

        return $includes;
    }
}
