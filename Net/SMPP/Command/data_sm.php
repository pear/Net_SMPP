<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * SMPP v3.4 data_sm command class
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
 * data_sm class
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
class Net_SMPP_Command_Data_Sm extends Net_SMPP_Command
{
    /**
     * @see Net_SMPP_Command_submit_sm::$service_type
     */
    var $service_type;

    /**
     * @see Net_SMPP_Command_submit_sm::$source_addr_ton
     */
    var $source_addr_ton;

    /**
     * @see Net_SMPP_Command_submit_sm::$source_addr_npi
     */
    var $source_addr_npi;

    /**
     * @see Net_SMPP_Command_submit_sm::$source_addr
     */
    var $source_addr;

    /**
     * @see Net_SMPP_Command_submit_sm::$dest_addr_ton
     */
    var $dest_addr_ton;

    /**
     * @see Net_SMPP_Command_submit_sm::$dest_addr_npi
     */
    var $dest_addr_npi;

    /**
     * @see Net_SMPP_Command_submit_sm::$destination_addr
     */
    var $destination_addr;

    /**
     * @see Net_SMPP_Command_submit_sm::$esm_class
     */
    var $esm_class;

    /**
     * @see Net_SMPP_Command_submit_sm::$registered_delivery
     */
    var $registered_delivery;

    /**
     * @see Net_SMPP_Command_submit_sm::$data_coding
     */
    var $data_coding;

    // Optional
    var $source_port;
    var $source_addr_subunit;
    var $source_network_type;
    var $source_bearer_type;
    var $source_telematics_id;
    var $destination_port;
    var $dest_addr_subunit;
    var $dest_network_type;
    var $dest_bearer_type;
    var $dest_telematics_id;
    var $sar_msg_ref_num;
    var $sar_total_segments;
    var $sar_segment_seqnum;
    var $more_messages_to_send;
    var $qos_time_to_live;
    var $payload_type;
    var $message_payload;
    var $receipted_message_id;
    var $message_state;
    var $network_error_code;
    var $user_message_reference;
    var $privacy_indicator;
    var $callback_num;
    var $callback_num_pres_ind;
    var $callback_num_atag;
    var $source_subaddress;
    var $dest_subaddress;
    var $user_response_code;
    var $display_time;
    var $sms_signal;
    var $ms_validity;
    var $ms_msg_wait_facilities;
    var $number_of_messages;
    var $alert_on_msg_delivery;
    var $language_indicator;
    var $its_reply_type;
    var $its_session_info;

    /**
     * Paramater definitions for this command
     *
     * @var     array
     * @access  protected
     * @see     Net_SMPP_Command::$_defs
     */
    var $_defs = array(
        'service_type' => array(
            'type' => 'string',
            'max' => 6
        ),
        'source_addr_ton' => array(
            'type' => 'int',
            'size' => 1,
        ),
        'source_addr_npi' => array(
            'type' => 'int',
            'size' => 1
        ),
        'source_addr' => array(
            'type' => 'string',
            'max' => 21,
        ),
        'dest_addr_ton' => array(
            'type' => 'int',
            'size' => 1,
        ),
        'dest_addr_npi' => array(
            'type' => 'int',
            'size' => 1,
        ),
        'destination_addr' => array(
            'type' => 'string',
            'max' => 21
        ),
        'esm_class' => array(
            'type' => 'int',
            'size' => 1
        ),
        'registered_delivery' => array(
            'type' => 'int',
            'size' => 1,
        ),
        'data_coding' => array(
            'type' => 'int',
            'size' => 1
        ),
        // Optional params
        'source_port' => array(
            'type' => 'int',
            'size' => 2
        ),
        'source_addr_subunit' => array(
            'type' => 'int',
            'size' => 1
        ),
        'source_network_type' => array(
            'type' => 'int',
            'size' => 1
        ),
        'source_bearer_type' => array(
            'type' => 'int',
            'size' => 1
        ),
        'source_telematics_id' => array(
            'type' => 'int',
            'size' => 2
        ),
        'destination_port' => array(
            'type' => 'int',
            'size' => 2
        ),
        'dest_addr_subunit' => array(
            'type' => 'int',
            'size' => 1
        ),
        'dest_network_type' => array(
            'type' => 'int',
            'size' => 1
        ),
        'dest_bearer_type' => array(
            'type' => 'int',
            'size' => 1
        ),
        'dest_telematics_id' => array(
            'type' => 'int',
            'size' => 2
        ),
        'sar_msg_ref_num' => array(
            'type' => 'int',
            'size' => 2
        ),
        'sar_total_segments' => array(
            'type' => 'int',
            'size' => 1
        ),
        'sar_segment_seqnum' => array(
            'type' => 'int',
            'size' => 1
        ),
        'more_messages_to_send' => array(
            'type' => 'int',
            'size' => 1
        ),
        'qos_time_to_live' => array(
            'type' => 'int',
            'size' => 4
        ),
        'payload_type' => array(
            'type' => 'int',
            'size' => 1
        ),
        'message_payload' => array(
            'type' => 'ostring',
            'max' => 260
        ),
        'receipted_message_id' => array(
            'type' => 'string',
            'max' => 65
        ),
        'message_state' => array(
            'type' => 'int',
            'size' => 1
        ),
        'network_error_code' => array(
            'type' => 'ostring',
            'size' => 3
        ),
        'user_message_reference' => array(
            'type' => 'int',
            'size' => 2
        ),
        'privacy_indicator' => array(
            'type' => 'int',
            'size' => 1
        ),
        'callback_num' => array(
            'type' => 'sting',
            'min' => 4,
            'max' => 19
        ),
        'callback_num_pres_ind' => array(
            'type' => 'int',
            'size' => 1
        ),
        'callback_num_atag' => array(
            'type' => 'string',
            'max' => 65
        ),
        'source_subaddress' => array(
            'type' => 'string',
            'min' => 2,
            'max' => 23
        ),
        'dest_subaddress' => array(
            'type' => 'string',
            'min' => 2,
            'max' => 23
        ),
        'user_response_code' => array(
            'type' => 'int',
            'size' => 1
        ),
        'display_time' => array(
            'type' => 'int',
            'size' => 1
        ),
        'sms_signal' => array(
            'type' => 'int',
            'size' => 2
        ),
        'ms_validity' => array(
            'type' => 'int',
            'size' => 1
        ),
        'ms_msg_wait_facilities' => array(
            'type' => 'int',
            'size' => 1
        ),
        'number_of_messages' => array(
            'type' => 'int',
            'size' => 1
        ),
        'alert_on_msg_delivery' => array(
            'type' => 'flag'
        ),
        'language_indicator' => array(
            'type' => 'int',
            'size' => 1
        ),
        'its_reply_type' => array(
            'type' => 'int',
            'size' => 1
        ),
        'its_session_info' => array(
            'type' => 'int',
            'size' => 2
        )
    );
}
