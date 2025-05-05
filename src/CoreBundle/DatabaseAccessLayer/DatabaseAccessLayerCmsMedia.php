<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DatabaseAccessLayer;

class DatabaseAccessLayerCmsMedia extends AbstractDatabaseAccessLayer
{
    /**
     * @var bool
     */
    private $isLoaded = false;

    /**
     * @param string $mediaId
     *
     * @return \TdbCmsMedia
     */
    public function loadMediaFromId($mediaId)
    {
        $this->loadAllParameters();

        $media = $this->getFromCache($mediaId);
        if (null !== $media) {
            return $media;
        }

        return \TdbCmsMedia::GetNewInstance($mediaId);
    }

    /**
     * @return void
     */
    private function loadAllParameters()
    {
        if (true === $this->isLoaded) {
            return;
        }
        $this->isLoaded = true;

        $query = 'SELECT * FROM `cms_media` WHERE cmsident < 1000';
        $mediaList = $this->getDatabaseConnection()->fetchAllAssociative($query);
        foreach ($mediaList as $mediaData) {
            $mediaObject = \TdbCmsMedia::GetNewInstance($mediaData);
            $this->setCache($mediaObject->id, $mediaObject);
        }
    }
}
