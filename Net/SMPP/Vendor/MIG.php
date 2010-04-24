<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MIG vendor support class and data
 *
 * PHP versions 4 and 5
 *
 */

// Place includes, constant defines and $_GLOBAL settings here.
require_once 'Net/SMPP/Vendor.php';

 /**
 * Possible values for MIG_operator
 *
 *
 * Connectionname (TLV 1600)
 *
 * Orange Connection - MIG01OU:     160000074D494730314F55
 * O2 Connection - MIG01XU:     160000074D494730315855
 * Three Connection - MIG01HU:     160000074D494730314855
 * T-Mobile Connection - MIG01TU:     160000074D494730315455
 * Vodafone Connection - MIG67VU:     160000074D494736375655
 *
 */

 /** The old mblox.php had this
 	So I think I should be putting in the actual characters because the hex will be converted????
 **/
//define('NET_SMPP_MBLOX_OPERATOR_ATT',      '31001');

define('NET_SMPP_MIG_OPERATOR_O2',     'MIG01XU');
define('NET_SMPP_MIG_OPERATOR_TMOB',   'MIG01TU');
define('NET_SMPP_MIG_OPERATOR_VODA',   'MIG67VU');
define('NET_SMPP_MIG_OPERATOR_ORANGE', 'MIG01OU');
define('NET_SMPP_MIG_OPERATOR_3',      'MIG01HU');




/**
 * mBlox submit_sm rejection codes
 *
 * I do not have any MIG at the moment.
 * But we are not currently sending via MIG so this can wait
 */
/*define('ESME_MBLOX_ERR_SYSTEM',                  0x0008);
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
*/

/**
 * MIG vendor support class
 *
 */
class Net_SMPP_Vendor_MIG extends Net_SMPP_Vendor {
    /**
     * MIG optional paramaters
     *
     * @var     array
     * @access  protected
     * @static
     *
     * TLV		  Description										Example
     * 1600       Connectionname (MT/MO)  (network)                 16000008xxxxxxxxxxxxxxxx
	 * 1601       Tariff exclusive VAT (ex 92) (MT)                 160100023932
	 * 1602       Tariff class (MT) (Not USED for now)              n.a.
	 * 1610       Operator country code (NL=204) (MT/MO)            16100003323034
	 * 1611       Operator network code (KPN=08) (MT/MO)            161100023038
	 * 1620       GUID (internal reference) (MO)                    1620......
	 * 1630       User ID (MT/DN)                                   16300006474230303031
	 * 1640       Subscription ID (MT)                              1640000131
	 * 1650       Billing text (MT)                                 1650000454455354
     */

    function &_optionalParams()
    {
        static $params = array(
            'MIG_operator'  => 0x1600,
            'MIG_guid'    => 0x1620
            //'MIG_tariff'    => 0x1601,
            //'MIG_tariff_class'    => 0x16202,
            //'MIG_operator_country'    => 0x1610,
            //'MIG_operator_network'    => 0x1611,
            //'MIG_User_ID'    => 0x1630,
            //'MIG_Subscription'    => 0x1640,
            //'MIG_Billing_Text'    => 0x1650
        );

        return $params;
    }




    /**
     * MIG status descriptions - left over from mblox.php
     *
     * @return  array
     * @access  protected
     * @static
     */

  /*  function &_statusDescs()
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
    }*/
}


?>
