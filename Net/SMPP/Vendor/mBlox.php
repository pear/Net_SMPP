<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * mBlox vendor support class and data
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
 * @since      0.2.5dev7
 * @link       http://pear.php.net/package/Net_SMPP
 */

// Place includes, constant defines and $_GLOBAL settings here.
require_once 'Net/SMPP/Vendor.php';

/**
 * Possible values for mblox_operator
 *
 */
define('NET_SMPP_MBLOX_OPERATOR_ATT',      '31001');
define('NET_SMPP_MBLOX_OPERATOR_CINGULAR', '31002');
define('NET_SMPP_MBLOX_OPERATOR_VERIZON',  '31003');
define('NET_SMPP_MBLOX_OPERATOR_TMOBILE',  '31004');
define('NET_SMPP_MBLOX_OPERATOR_SPRINT',   '31005');

/**
 * mBlox submit_sm rejection codes
 *
 */
define('ESME_MBLOX_ERR_SYSTEM',                  0x0008);
define('ESME_MBLOX_ERR_NUM_BLACKLISTED',         0x0401);
define('ESME_MBLOX_ERR_CLNT_BLACKLISTED',        0x0402);
define('ESME_MBLOX_ERR_PRE_BLACKLISTED',         0x0403);
define('ESME_MBLOX_ERR_INVALID_ACCOUNT',         0x0404);
define('ESME_MBLOX_ERR_BUSY',                    0x0406);
define('ESME_MBLOX_ERR_REPLY_TYPE',              0x0407);
define('ESME_MBLOX_ERR_MSIP_SYNTAX',             0x0408);
define('ESME_MBLOX_ERR_SYSTEM_UNAVAIL1',         0x040A);
define('ESME_MBLOX_ERR_SYSTEM_UNAVAIL2',         0x040B);
define('ESME_MBLOX_ERR_SYSTEM_UNAVAIL3',         0x040C);
define('ESME_MBLOX_ERR_PROFILE',                 0x040D);
define('ESME_MBLOX_ERR_NO_USERNAME',             0x040E);
define('ESME_MBLOX_ERR_NO_BINARY',               0x040F);
define('ESME_MBLOX_ERR_TEMP_FAIL',               0x0410);
define('ESME_MBLOX_ERR_NUM_UNROUTABLE',          0x0411);
define('ESME_MBLOX_ERR_TEMP_NUM_UNROURABLE',     0x0412);
define('ESME_MBLOX_ERR_NUM_UNROUTABLE2',         0x0413);
define('ESME_MBLOX_ERR_NUM_UNROUTABLE_SETTINGS', 0x0414);
define('ESME_MBLOX_ERR_TEMP_NUM_UNROUTABLE2',    0x0415);
define('ESME_MBLOX_ERR_NUM_UNROUTABLE3',         0x0416);
define('ESME_MBLOX_ERR_NUM_UNROUTABLE4',         0x0417);
define('ESME_MBLOX_ERR_NUM_UNROUTABLE5',         0x0418);
define('ESME_MBLOX_ERR_NUM_UNROUTABLE6',         0x0419);
define('ESME_MBLOX_ERR_NUM_UNROUTABLE7',         0x041A);
define('ESME_MBLOX_ERR_NUM_UNROUTABLE8',         0x041B);
define('ESME_MBLOX_ERR_TEMP_NUM_UNROUTABLE3',    0x041C);
define('ESME_MBLOX_ERR_NUM_UNROUTABLE9',         0x041D);
define('ESME_MBLOX_ERR_TEMP_NUM_UNROUTABLE4',    0x041E);
define('ESME_MBLOX_ERR_TEMP_NUM_UNROUTABLE5',    0x041F);
define('ESME_MBLOX_ERR_UNABLE_TO_SEND',          0x0420);
define('ESME_MBLOX_ERR_NO_ORIGINATOR',           0x0421);
define('ESME_MBLOX_ERR_DEST_TRY_AGAIN',          0x0422);
define('ESME_MBLOX_ERR_BLOCKED',                 0x0423);
define('ESME_MBLOX_ERR_BILLING',                 0x0424);
define('ESME_MBLOX_ERR_BLOCKED2',                0x0425);
define('ESME_MBLOX_ERR_THROTTLING',              0x0426);
define('ESME_MBLOX_ERR_BAD_SEQ',                 0x0427);
define('ESME_MBLOX_ERR_CLIENT_ID',               0x0428);
define('ESME_MBLOX_ERR_CLIENT_ID2',              0x0429);
define('ESME_MBLOX_ERR_PSMS_ROUTING',            0x042A);
define('ESME_MBLOX_ERR_PSMS_ROUTING2',           0x042B);
define('ESME_MBLOX_ERR_PSMS_ROUTING3',           0x042C);
define('ESME_MBLOX_ERR_PSMS_ROUTING4',           0x042D);


/**
 * mBlox vendor support class
 *
 * @category   Networking
 * @package    Net_SMPP
 * @author     Ian Eure <ieure@php.net>
 * @copyright  (c) Copyright 2005 WebSprockets, LLC.
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @version    CVS:     $Revision$
 * @since      0.3.4dev1
 * @link       http://pear.php.net/package/Net_SMPP
 */
class Net_SMPP_Vendor_mBlox extends Net_SMPP_Vendor {
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
            'mblox_operator'  => 0x1402,
            'mblox_tariff'    => 0x1403,
            'mblox_sessionid' => 0x1404
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
            ESME_MBLOX_ERR_SYSTEM                  => 'System Error',
            ESME_MBLOX_ERR_NUM_BLACKLISTED         => 'Number blacklisted in system',
            ESME_MBLOX_ERR_CLNT_BLACKLISTED        => 'Client blacklisted in system',
            ESME_MBLOX_ERR_PRE_BLACKLISTED         => 'Prefix blacklisted in system',
            ESME_MBLOX_ERR_INVALID_ACCOUNT         => 'Invalid account Error',
            ESME_MBLOX_ERR_BUSY                    => 'Destination busy - The message was not sent due to the fact that the QoS was busy , please try again.
        ',
            ESME_MBLOX_ERR_REPLY_TYPE              => 'Reply Type Error.',
            ESME_MBLOX_ERR_MSIP_SYNTAX             => 'MSIP Syntax Error.',
            ESME_MBLOX_ERR_SYSTEM_UNAVAIL1         => 'System unavailable.',
            ESME_MBLOX_ERR_SYSTEM_UNAVAIL2         => 'System unavailable.',
            ESME_MBLOX_ERR_SYSTEM_UNAVAIL3         => 'System unavailable.',
            ESME_MBLOX_ERR_PROFILE                 => 'Profile Error.',
            ESME_MBLOX_ERR_NO_USERNAME             => 'Username not set - No username was specified.',
            ESME_MBLOX_ERR_NO_BINARY               => 'Do not try again. Binary message not allowed on profile. - This message does not allow binary messages.',
            ESME_MBLOX_ERR_TEMP_FAIL               => 'Temporary System failure, please retry.',
            ESME_MBLOX_ERR_NUM_UNROUTABLE          => 'Number unroutable. Do not retry.',
            ESME_MBLOX_ERR_TEMP_NUM_UNROURABLE     => 'Number Temporarily unroutable, please try again.',
            ESME_MBLOX_ERR_NUM_UNROUTABLE2         => 'Number unroutable. Do not retry.',
            ESME_MBLOX_ERR_NUM_UNROUTABLE_SETTINGS => 'Number unroutable on current settings. Do not retry.',
            ESME_MBLOX_ERR_TEMP_NUM_UNROUTABLE2    => 'Number Temporarily unroutable, please try again.',
            ESME_MBLOX_ERR_NUM_UNROUTABLE3         => 'Number unroutable. Do not retry.',
            ESME_MBLOX_ERR_NUM_UNROUTABLE4         => 'Number unroutable. Do not retry.',
            ESME_MBLOX_ERR_NUM_UNROUTABLE5         => 'Number unroutable. Do not retry.',
            ESME_MBLOX_ERR_NUM_UNROUTABLE6         => 'Number unroutable. Do not retry.',
            ESME_MBLOX_ERR_NUM_UNROUTABLE7         => 'Number unroutable. Do not retry.',
            ESME_MBLOX_ERR_NUM_UNROUTABLE8         => 'Number unroutable. Do not retry.',
            ESME_MBLOX_ERR_TEMP_NUM_UNROUTABLE3    => 'Number Temporarily unroutable, please try again.',
            ESME_MBLOX_ERR_NUM_UNROUTABLE9         => 'Number unroutable. Do not retry.',
            ESME_MBLOX_ERR_TEMP_NUM_UNROUTABLE4    => 'Number Temporarily unroutable, please try again.',
            ESME_MBLOX_ERR_TEMP_NUM_UNROUTABLE5    => 'Number Temporarily unroutable, please try again.',
            ESME_MBLOX_ERR_UNABLE_TO_SEND          => 'Unable to send on local deliverer',
            ESME_MBLOX_ERR_NO_ORIGINATOR           => 'Cannot find originator for index. Do not retry.',
            ESME_MBLOX_ERR_DEST_TRY_AGAIN          => 'Destination please try again.',
            ESME_MBLOX_ERR_BLOCKED                 => 'Number is blocked. Do not retry.',
            ESME_MBLOX_ERR_BILLING                 => 'Billing Reference Error. Do not retry.',
            ESME_MBLOX_ERR_BLOCKED2                => 'Number is blocked. Do not retry',
            ESME_MBLOX_ERR_THROTTLING              => 'Throttling – Please try again.',
            ESME_MBLOX_ERR_BAD_SEQ                 => 'Bad sequence',
            ESME_MBLOX_ERR_CLIENT_ID               => 'Error when supplying a client id',
            ESME_MBLOX_ERR_CLIENT_ID2              => 'Error when supplying a client id',
            ESME_MBLOX_ERR_PSMS_ROUTING            => 'Routing error for PSMS',
            ESME_MBLOX_ERR_PSMS_ROUTING2           => 'Routing error for PSMS',
            ESME_MBLOX_ERR_PSMS_ROUTING3           => 'Routing error for PSMS',
            ESME_MBLOX_ERR_PSMS_ROUTING4           => 'Routing error for PSMS'
        );

        return $descs;
    }
}


?>