<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ImageNotFoundController
{
    /**
     * @return BinaryFileResponse|Response
     */
    public function __invoke()
    {
        $imagePath = PATH_WEB.CHAMELEON_404_IMAGE_PATH_SMALL;
        if (file_exists($imagePath)) {
            return new BinaryFileResponse($imagePath);
        }

        return new Response('', Response::HTTP_NOT_FOUND);
    }
}
