<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsCaptcha extends TPkgCmsCaptchaAutoParent
{
    public const URL_IDENTIFIER = '_cmscaptcha_';

    /**
     * factory creates a new instance and returns it.
     *
     * @param string|array $sData - either the id of the object to load, or the row with which the instance should be initialized
     * @param string $sLanguage - init with the language passed
     */
    public static function GetNewInstance($sData = null, $sLanguage = null): TdbPkgCmsCaptcha
    {
        $oObject = parent::GetNewInstance($sData, $sLanguage);
        if ($oObject && !empty($oObject->id) && !empty($oObject->fieldClass)) {
            $aData = $oObject->sqlData;
            $sClassName = $oObject->fieldClass;
            $oObject = new $sClassName();
            if (!is_null($sLanguage)) {
                $oObject->SetLanguage($sLanguage);
            }
            $oObject->LoadFromRow($aData);
        }

        return $oObject;
    }

    /**
     * returns the path to TTF font file, that is used for the captcha.
     *
     * @return string
     */
    protected function GetFontPath()
    {
        return realpath(dirname(__FILE__).'/../../font/monofont.ttf');
    }

    /**
     * generates the captcha image and outputs it.
     *
     * @param string $sIdentifier
     * @param array $aParameter
     *
     * @return void
     */
    public function GenerateNewCaptchaImage($sIdentifier, $aParameter = [])
    {
        $iLength = 6;
        $iWidth = 120;
        $iHeight = 40;
        if (array_key_exists('l', $aParameter)) {
            $iLength = intval($aParameter['l']);
        }
        if (array_key_exists('w', $aParameter)) {
            $iWidth = intval($aParameter['w']);
        }
        if (array_key_exists('h', $aParameter)) {
            $iHeight = intval($aParameter['h']);
        }

        $code = $this->GenerateCode($sIdentifier, $iLength);
        $font_size = $iHeight * 0.75; // 3/4 font size

        if ($this->useImagickForCaptchaImage()) {
            $this->generateCaptchaImageImagick($iWidth, $iHeight, $font_size, $code);
        } else {
            $this->generateCaptchaImageGd($iWidth, $iHeight, $font_size, $code);
        }
    }

    /**
     * @return bool
     */
    protected function useImagickForCaptchaImage()
    {
        $config = TdbCmsConfig::GetInstance();

        return (!DISABLE_IMAGEMAGICK && false !== $config->GetImageMagickVersion()) || defined('HHVM_VERSION');
    }

    /**
     * @param int $iWidth
     * @param int $iHeight
     * @param float $font_size
     * @param string $code
     *
     * @return void
     */
    protected function generateCaptchaImageGd($iWidth, $iHeight, $font_size, $code)
    {
        $image = imagecreate($iWidth, $iHeight);
        /* set the colours */
        $bg_color = imagecolorallocate($image, 255, 255, 255);
        $text_color = imagecolorallocate($image, 20, 40, 100);
        $noise_color = imagecolorallocate($image, 20, 40, 100);
        //      $noise_color = imagecolorallocate($image, 100, 120, 180);
        imagefill($image, 0, 0, $bg_color);
        /* generate random dots in background */
        for ($i = 0; $i < ($iWidth * $iHeight) / 5; ++$i) {
            imagefilledellipse($image, mt_rand(0, $iWidth), mt_rand(0, $iHeight), 1, 1, $noise_color);
        }
        /* generate random lines in background */
        for ($i = 0; $i < ($iWidth * $iHeight) / 200; ++$i) {
            imageline($image, mt_rand(0, $iWidth), mt_rand(0, $iHeight), mt_rand(0, $iWidth), mt_rand(0, $iHeight), $noise_color);
        }
        /* create textbox and add text */
        $textbox = imagettfbbox($font_size, 0, $this->GetFontPath(), $code);
        $x = ($iWidth - $textbox[4]) / 2;
        $y = ($iHeight - $textbox[5]) / 2;
        imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->GetFontPath(), $code);
        /* output captcha image to browser */

        header('Content-Type: image/jpeg');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: Sat, 20 Jul 2000 05:00:00 GMT');
        imagejpeg($image);
        imagedestroy($image);
    }

    /**
     * @param int $iWidth
     * @param int $iHeight
     * @param float $font_size
     * @param string $code
     *
     * @return void
     */
    protected function generateCaptchaImageImagick($iWidth, $iHeight, $font_size, $code)
    {
        header('Content-Type: image/jpeg');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: Sat, 20 Jul 2000 05:00:00 GMT');

        $image = new Imagick();

        $bg_color = '#fff';
        $text_color = 'rgb(20, 40, 100)';
        $noise_color = 'rgb(20, 40, 100)';

        $image->newImage($iWidth, $iHeight, new ImagickPixel($bg_color));
        $image->setImageFormat('png');

        /* generate random dots in background */
        for ($i = 0; $i < ($iWidth * $iHeight) / 5; ++$i) {
            $draw = new ImagickDraw();
            $pixel = new ImagickPixel($noise_color);
            $draw->setFillColor($pixel);
            $draw->point(mt_rand(0, $iWidth), mt_rand(0, $iHeight));
            $image->drawimage($draw);
        }

        /* generate random lines in background */
        for ($i = 0; $i < ($iWidth * $iHeight) / 200; ++$i) {
            $draw = new ImagickDraw();
            $pixel = new ImagickPixel($noise_color);
            $draw->setFillColor($pixel);
            $draw->line(mt_rand(0, $iWidth), mt_rand(0, $iHeight), mt_rand(0, $iWidth), mt_rand(0, $iHeight));
            $image->drawimage($draw);
        }

        $draw = new ImagickDraw();
        $draw->setFont($this->GetFontPath());
        $draw->setFontSize($font_size);
        $draw->setFillColor($text_color);
        $draw->setgravity(Imagick::GRAVITY_CENTER);
        $image->annotateImage($draw, 0, 0, 0, $code);

        echo $image->getImageBlob();
    }

    /**
     * saves the captcha value in session.
     *
     * @param string $sIdentifier
     * @param scalar $sCode
     *
     * @return void
     */
    protected static function SaveInSession($sIdentifier, $sCode)
    {
        if (!array_key_exists(self::URL_IDENTIFIER, $_SESSION)) {
            $_SESSION[self::URL_IDENTIFIER] = [];
        }
        $_SESSION[self::URL_IDENTIFIER][$sIdentifier] = $sCode;
    }

    /**
     * loads the captcha value in session.
     *
     * @param string $sIdentifier
     *
     * @return string|false
     */
    protected static function GetCodeFromSession($sIdentifier)
    {
        $sCode = false;
        if (array_key_exists(self::URL_IDENTIFIER, $_SESSION) && array_key_exists($sIdentifier, $_SESSION[self::URL_IDENTIFIER])) {
            $sCode = $_SESSION[self::URL_IDENTIFIER][$sIdentifier];
            unset($_SESSION[self::URL_IDENTIFIER][$sIdentifier]);
        }

        return $sCode;
    }

    /**
     * return true if the code in session for the identifier is the same as the code passed. note: the code will be removed from session.
     *
     * @param string $sIdentifier
     * @param string $sCode
     *
     * @return bool
     */
    public function CodeIsValid($sIdentifier, $sCode)
    {
        $sCodeInSession = TdbPkgCmsCaptcha::GetCodeFromSession($sIdentifier);
        if (false === $sCodeInSession) {
            return false;
        }

        return $sCodeInSession == $sCode;
    }

    /**
     * generates a code for an identifier only once within one session call.
     *
     * @param string $sIdentifier
     * @param int $iCharacters
     *
     * @return string
     */
    protected function GenerateCode($sIdentifier, $iCharacters)
    {
        /** @var array<string, string> $aCodeCache */
        static $aCodeCache = [];
        if (!array_key_exists($sIdentifier, $aCodeCache)) {
            $possible = '23456789bcdfghjkmnpqrstvwxyz';
            $code = '';
            $i = 0;
            while ($i < $iCharacters) {
                $code .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
                ++$i;
            }
            $aCodeCache[$sIdentifier] = $code;
        }
        TdbPkgCmsCaptcha::SaveInSession($sIdentifier, $aCodeCache[$sIdentifier]);

        return $aCodeCache[$sIdentifier];
    }

    /**
     * returns input field type text by default.
     *
     * @param string $sIdentifier used as name and id for the input field
     *
     * @return string
     */
    public function getHTMLSnippet($sIdentifier)
    {
        return '<input type="text" name="'.$sIdentifier.'" id="'.$sIdentifier.'" value="" />';
    }

    /**
     * generates the url that creates a random captcha.
     *
     * @param string $sIdentifier
     * @param array $aParameter
     *
     * @return string
     */
    public function GetRequestURL($sIdentifier, $aParameter = [])
    {
        $sURL = '/'.self::URL_IDENTIFIER.'/'.$this->sqlData['cmsident'].'/'.$sIdentifier;
        $aParameter['rnd'] = rand(1000000, 9999999);
        if (count($aParameter) > 0) {
            $sURL .= '?'.TTools::GetArrayAsURL($aParameter);
        }

        return $sURL;
    }

    /**
     * loads a pkgCaptcha class by type name.
     *
     * @static
     *
     * @param string $sName
     *
     * @return TdbPkgCmsCaptcha
     */
    public static function GetInstanceFromName($sName)
    {
        static $aCache = [];
        if (false == array_key_exists($sName, $aCache)) {
            $oInstance = TdbPkgCmsCaptcha::GetNewInstance();
            if ($oInstance->LoadFromField('name', $sName)) {
                if (!empty($oInstance->fieldClass)) {
                    $oInstance = TdbPkgCmsCaptcha::GetNewInstance($oInstance->sqlData);
                }
            }
            $aCache[$sName] = $oInstance;
        }

        return $aCache[$sName];
    }

    /**
     * loads a pkgCaptcha class by cmsident id.
     *
     * @param int $iCmsIdent
     *
     * @return TdbPkgCmsCaptcha
     */
    public static function GetInstanceFromCmsIdent($iCmsIdent)
    {
        static $aCache = [];
        if (false == array_key_exists($iCmsIdent, $aCache)) {
            $oInstance = TdbPkgCmsCaptcha::GetNewInstance();
            if ($oInstance->LoadFromField('cmsident', $iCmsIdent)) {
                if (!empty($oInstance->fieldClass)) {
                    $oInstance = TdbPkgCmsCaptcha::GetNewInstance($oInstance->sqlData);
                }
            }
            $aCache[$iCmsIdent] = $oInstance;
        }

        return $aCache[$iCmsIdent];
    }
}
