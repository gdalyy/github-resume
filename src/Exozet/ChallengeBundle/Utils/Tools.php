<?php

namespace Exozet\ChallengeBundle\Utils;

/**
 * Exozet Challenge : tool box
 *
 * @author Ghaith Daly <https://www.linkedin.com/in/ghaith-daly-352006152/>
 */
class Tools
{
    /**
     * Formatting float to 2 decimals numbers
     * 
     * @param $number
     * @return string
     */
    public static function twoDecimalFloat($number)
    {
        return number_format((float)$number, 2, '.', ''); 
    }

    /**
     * sorting array DESC based on percentage value
     * 
     * @param $x
     * @param $y
     * @return mixed
     */
    public static function sortByPercentage($x, $y) {
        return $y['percentage'] - $x['percentage'];
    }

}
