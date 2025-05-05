<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\UniversalUploader\Bridge\Chameleon;

use ChameleonSystem\CoreBundle\UniversalUploader\Library\DataModel\UploadedFileDataModel;

/**
 * Save the uploaded file in the chameleon media/document pool.
 * Set errors and record id of saved record on $uploadedFile and return it again.
 *
 * Interface SaveToMediaLibraryServiceInterface
 */
interface SaveToMediaLibraryServiceInterface
{
    /**
     * @return UploadedFileDataModel
     */
    public function saveUploadedFile(UploadedFileDataModel $uploadedFile);
}
