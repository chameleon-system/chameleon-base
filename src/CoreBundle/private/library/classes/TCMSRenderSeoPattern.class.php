<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSRenderSeoPattern
{
    /**
     * Pattern replace values.
     *
     * @var array
     */
    protected $aPatternRepl = [];

    /**
     * Class constructor.
     */
    public function __construct()
    {
    }

    /**
     * Set pattern replace values.
     *
     * If calling this function more then one times -  replacement values will be added up
     *
     * @param array $prepl_in values key=value
     */
    public function AddPatternReplaceValues($prepl_in)
    {
        if (is_array($prepl_in) && count($prepl_in)) {
            $this->aPatternRepl = array_merge($this->aPatternRepl, $prepl_in);
        }
    }

    /**
     * Render SEO Pattern.
     *
     * @param string $sSeoPattern
     *
     * @return string
     */
    public function RenderPattern($sSeoPattern)
    {
        $sSeoPattern = trim($sSeoPattern);

        $sFound = stristr($sSeoPattern, '[{SHOW}]');
        if (!$sFound) {
            if (strlen($sSeoPattern) > 0) {
                // replace all
                foreach ($this->aPatternRepl as $k => $v) {
                    $sToRepl = '[{'.$k.'}]';
                    $sSeoPattern = str_ireplace($sToRepl, $v, $sSeoPattern);
                }
                // clean all - not replaced -
                $sSeoPattern = preg_replace('/\[{.*}\]/i', '', $sSeoPattern);
            }
        } else {
            // show all values
            $sSeoPattern = '';
            foreach ($this->aPatternRepl as $k => $v) {
                $sSeoPattern .= ' [{'.$k.'}]';
            }
            $sSeoPattern .= ' [{SHOW}]';
        }

        return $sSeoPattern;
    }
}
