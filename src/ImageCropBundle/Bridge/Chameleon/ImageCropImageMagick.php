<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCropBundle\Bridge\Chameleon;

use ChameleonSystem\ImageCrop\Exception\ImagickException;

/**
 * {@inheritdoc}
 */
class ImageCropImageMagick extends \imageMagick
{
    /**
     * @param int $width
     * @param int $height
     * @param int $xPos
     * @param int $yPos
     *
     * @return bool - use boolean return value instead of exception to behave like parent class
     */
    public function cropImageWithCoordinates($width, $height, $xPos = 0, $yPos = 0)
    {
        $this->iThumbWidth = $width;
        $this->iThumbHeight = $height;

        if (true === $this->bHasErrors) {
            return false;
        }

        try {
            if (true === $this->bUsePHPLibrary) {
                $this->cropImageWithCoordinatesUsingImagickExtension($width, $height, $xPos, $yPos);
            } else {
                $this->cropImageWithCoordinatesUsingImagickViaShellCommand($width, $height, $xPos, $yPos);
            }
            $this->oSourceFile->sPath = $this->sTempDir.'/'.$this->sTempFileName;
        } catch (ImagickException $e) {
            $this->AddError($e->getMessage());
        }

        return true;
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $xPos
     * @param int $yPos
     *
     * @return void
     *
     * @throws ImagickException
     */
    protected function cropImageWithCoordinatesUsingImagickExtension($width, $height, $xPos = 0, $yPos = 0)
    {
        $cropWorked = $this->oIMagick->cropImage($width, $height, $xPos, $yPos);
        if (false === $cropWorked) {
            throw new ImagickException(sprintf('could not crop image using php extension: cropImage() failed.'));
        }

        $writeWorked = $this->oIMagick->writeImage($this->sTempDir.'/'.$this->sTempFileName);
        if (false === $writeWorked) {
            throw new ImagickException(sprintf('could not crop image using php extension: writeImage() failed.'));
        }

        $this->oIMagick->destroy();
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $xPos
     * @param int $yPos
     *
     * @return void
     *
     * @throws ImagickException
     */
    protected function cropImageWithCoordinatesUsingImagickViaShellCommand($width, $height, $xPos = 0, $yPos = 0)
    {
        $command = $this->sImageMagickDir.'/convert '.escapeshellarg(
            $this->oSourceFile->sPath
        ).' -auto-orient -crop '.escapeshellarg($width.'x'.$height.'+'.$xPos.'+'.$yPos).' +repage '.escapeshellarg(
            $this->sTempDir.'/'.$this->sTempFileName
        );
        $command = str_replace('//', '/', $command);
        exec($command, $returnArray, $returnValue);

        if ($returnValue) {
            throw new ImagickException(sprintf('could not crop image using shell command: %s', $command));
        }
    }
}
