<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCoreUtility_Network
{
    public const IP_RANGE_TYPE_CIDR = 1;
    public const IP_RANGE_TYPE_WILDCARD = 2;
    public const IP_RANGE_TYPE_RANGE = 3;
    public const IP_RANGE_TYPE_NONE = 4;

    /**
     * @param string $string
     *
     * @return int
     *
     * @psalm-return self::IP_RANGE_TYPE_*
     */
    public function getRangeType($string)
    {
        if (false !== strpos($string, '/')) {
            return self::IP_RANGE_TYPE_CIDR;
        }

        if (false !== strpos($string, '*')) {
            return self::IP_RANGE_TYPE_WILDCARD;
        }

        if (false !== strpos($string, '-')) {
            return self::IP_RANGE_TYPE_RANGE;
        }

        return self::IP_RANGE_TYPE_NONE;
    }

    // *****************************************************************************************************************
    /*
     * ip_in_range.php - Function to determine if an IP is located in a
     *                   specific range as specified via several alternative
     *                   formats.
     *
     * Network ranges can be specified as:
     * 1. Wildcard format:     1.2.3.*
     * 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
     * 3. Start-End IP format: 1.2.3.0-1.2.3.255
     *
     * Return value BOOLEAN : ip_in_range($ip, $range);
     *
     * Copyright 2008: Paul Gregg <pgregg@pgregg.com>
     * 10 January 2008
     * Version: 1.2
     *
     * Source website: http://www.pgregg.com/projects/php/ip_in_range/
     * Version 1.2
     *
     * This software is Donationware - if you feel you have benefited from
     * the use of this tool then please consider a donation. The value of
     * which is entirely left up to your discretion.
     * http://www.pgregg.com/donate/
     *
     * Please do not remove this header, or source attibution from this file.
     */
    // ip_in_range
    // This function takes 2 arguments, an IP address and a "range" in several
    // different formats.
    // Network ranges can be specified as:
    // 1. Wildcard format:     1.2.3.*
    // 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
    // 3. Start-End IP format: 1.2.3.0-1.2.3.255
    // The function will return true if the supplied IP is within the range.
    // Note little validation is done on the range inputs - it expects you to
    // use one of the above 3 formats.
    /**
     * @param string $ip
     * @param string $range
     *
     * @return bool
     */
    public function ipIsInRange($ip, $range)
    {
        $sFormat = $this->getRangeType($range);
        if (self::IP_RANGE_TYPE_NONE === $sFormat) {
            return false;
        }

        if (self::IP_RANGE_TYPE_CIDR === $sFormat) {
            // $range is in IP/NETMASK format
            /**
             * @var string $range
             * @var numeric-string $netmask
             */
            list($range, $netmask) = explode('/', $range, 2);
            if (false !== strpos($netmask, '.')) {
                // $netmask is a 255.255.0.0 format
                $netmask = str_replace('*', '0', $netmask);
                $netmask_dec = ip2long($netmask);

                return (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec);
            } else {
                // $netmask is a CIDR size block
                // fix the range argument
                $x = explode('.', $range);
                while (count($x) < 4) {
                    $x[] = '0';
                }
                list($a, $b, $c, $d) = $x;
                $range = sprintf(
                    '%u.%u.%u.%u',
                    empty($a) ? '0' : $a,
                    empty($b) ? '0' : $b,
                    empty($c) ? '0' : $c,
                    empty($d) ? '0' : $d
                );
                $range_dec = ip2long($range);
                $ip_dec = ip2long($ip);

                // Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
                // $netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

                // Strategy 2 - Use math to create it
                $wildcard_dec = pow(2, 32 - $netmask) - 1;
                $netmask_dec = ~$wildcard_dec;

                return ($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec);
            }
        } else {
            // range might be 255.255.*.* or 1.2.3.0-1.2.3.255
            if (self::IP_RANGE_TYPE_WILDCARD === $sFormat) { // a.b.*.* format
                // Just convert to A-B format by setting * to 0 for A and 255 for B
                $lower = str_replace('*', '0', $range);
                $upper = str_replace('*', '255', $range);
                $range = "$lower-$upper";
                $sFormat = self::IP_RANGE_TYPE_RANGE;
            }

            if (self::IP_RANGE_TYPE_RANGE === $sFormat) { // A-B format
                list($lower, $upper) = explode('-', $range, 2);
                $lower_dec = (float) sprintf('%u', ip2long($lower));
                $upper_dec = (float) sprintf('%u', ip2long($upper));
                $ip_dec = (float) sprintf('%u', ip2long($ip));

                return ($ip_dec >= $lower_dec) && ($ip_dec <= $upper_dec);
            }

            return false;
        }
    }

    // *****************************************************************************************************************
}
