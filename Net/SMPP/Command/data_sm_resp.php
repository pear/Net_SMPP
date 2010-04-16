<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * SMPP v3.4 data_sm_resp command class
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
 * @copyright  Portions of the documentation (c) Copyright 1999 SMPP Developers
 *             Forum.
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @version    CVS:     $Revision$
 * @since      Release 0.3.8
 * @link       http://pear.php.net/package/Net_SMPP
 */

// Place includes, constant defines and $_GLOBAL settings here.
require_once 'Net/SMPP/Command.php';

/**
 * data_sm_resp class
 *
 * This command is used to transfer data between the SMSC and the ESME. It may
 * be used by both the ESME and SMSC.
 *
 * This command is an alternative to the submit_sm and deliver_sm commands. It
 * is introduced as a new command to be used by interactive applications such
 * as those provided via a WAP framework.
 *
 * The ESME may use this command to request the SMSC to transfer a message to
 * an MS. The SMSC may also use this command to transfer an MS originated message
 * to an ESME.
 *
 * In addition, the data_sm operation can be used to transfer the following
 * types of special messages to the ESME:-
 *
 * - SMSC Delivery Receipt.
 * - SME Delivery Acknowledgement. The user data of the SME delivery acknowledgement
 *    is included in the short_message field of the data_sm
 * - SME Manual/User Acknowledgement. The user data of the SME delivery
 *   acknowledgement is included in the short_message field of the data_sm
 * - Intermediate Notification.
 *
 * @category   Networking
 * @package    Net_SMPP
 * @author     Ian Eure <ieure@php.net>
 * @copyright  (c) Copyright 2005 WebSprockets, LLC.
 * @copyright  Portions of the documentation (c) Copyright 1999 SMPP Developers
 *             Forum.
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @version    CVS:     $Revision$
 * @since      Release 0.3.8
 * @link       http://pear.php.net/package/Net_SMPP
 */
class Net_SMPP_Command_Data_Sm_Resp extends Net_SMPP_Command
{
    /**
     * This field contains the SMSC assigned message ID of the short message.
     *
     * @var  string
     */
    var $message_id;

    // Optional
    /**
     * Include to indicate reason for delivery failure.
     *
     * only relevant for transaction message mode.
     *
     * @var  fixme
     */
    var $delivery_failure_reason;

    /**
     * Error code specific to a wireless network.
     *
     * only relevant for transaction message mode.
     *
     * @var  fixme
     */
    var $network_error_code;

    /**
     * ASCII text giving a description of the meaning of the response
     *
     * @var  string
     */
    var $additional_status_info_text;

    /**
     * Indicates whether the Delivery Pending Flag was set.
     *
     * only relevant for transaction message mode.
     *
     * @var  fixme
     */
    var $dpf_result;
}
