<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @deprecated only needed for old search engine calls to redirect to new document url
 *
/**/
class CMSDeliverFile extends TCMSModelBase
{
    public function Execute()
    {
        parent::Execute();

        if ($this->global->UserDataExists('fileid')) {
            $oDocument = TdbCmsDocument::GetNewInstance();
            if ($oDocument->Load($this->global->GetUserData('fileid'))) {
                $sDownloadURL = $oDocument->GetPlainDownloadLink();
                $this->getRedirect()->redirect($sDownloadURL, Response::HTTP_MOVED_PERMANENTLY);
            } else {
                throw new NotFoundHttpException();
            }
        }

        return $this->data;
    }

    /**
     * @return ICmsCoreRedirect
     */
    private function getRedirect()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }
}
