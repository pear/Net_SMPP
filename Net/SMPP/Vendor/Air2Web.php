<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Air2Web support class and data
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Networking
 * @package    Net_SMPP
 * @author     Ian Eure <ieure@php.net>
 * @copyright  (c) Copyright 2005 WebSprockets, LLC.
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @version    CVS:     $Revision$
 * @since      0.4.5
 * @link       http://pear.php.net/package/Net_SMPP
 */

// Place includes, constant defines and $_GLOBAL settings here.
require_once 'Net/SMPP/Vendor.php';

/**
 * Possible values for Premium_content_indicator
 */
define('NET_SMPP_AIR2WEB_PREMIUM_099',  '7'); // $0.99
define('NET_SMPP_AIR2WEB_PREMIUM_150',  '9'); // $1.50
define('NET_SMPP_AIR2WEB_PREMIUM_199', '10'); // $1.99
define('NET_SMPP_AIR2WEB_PREMIUM_299', '13'); // $2.99
define('NET_SMPP_AIR2WEB_PREMIUM_399',  '3'); // $3.99
define('NET_SMPP_AIR2WEB_PREMIUM_499',  '4'); // $4.99

/**
 * Air2Web submit_sm rejection codes
 *
 */
define('ESME_AIR2WEB_RINVPREMCODE', 0x0403);
define('ESME_AIR2WEB_RNOTPREMPROV', 0x0404);


/**
 * Air2web vendor support class
 *
 * @category   Networking
 * @package    Net_SMPP
 * @author     Ian Eure <ieure@php.net>
 * @copyright  (c) Copyright 2005 WebSprockets, LLC.
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @version    CVS:     $Revision$
 * @since      0.4.5
 * @link       http://pear.php.net/package/Net_SMPP
 */
class Net_SMPP_Vendor_Air2Web extends Net_SMPP_Vendor {
    /**
     * mBlox optional paramaters
     *
     * @var     array
     * @access  protected
     * @static
     */
    function &_optionalParams()
    {
        static $params = array(
            'Premium_content_indicator'  => 0x1405
        );

        return $params;
    }

    /**
     * mBlox status descriptions
     *
     * @return  array
     * @access  protected
     * @static
     */
    function &_statusDescs()
    {
        static $descs = array(
            ESME_AIR2WEB_RINVPREMCODE => 'Invalid Premium Value',
            ESME_AIR2WEB_RNOTPREMPROV => 'Not Provisioned for Premium Messaging'
        );

        return $descs;
    }
}


?>