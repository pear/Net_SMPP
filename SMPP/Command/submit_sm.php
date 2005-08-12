<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * SMPP v3.4 submit_sm command class
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
 * @since      Release 0.0.1dev6
 * @link       http://pear.php.net/package/Net_SMPP
 */

// Place includes, constant defines and $_GLOBAL settings here.
require_once 'Net/SMPP/Command.php';

/**
 * submit_sm class
 *
 * This operation is used by an ESME to submit a short message to the SMSC for
 * onward transmission to a specified short message entity (SME). The submit_sm
 * PDU does not support the transaction message mode.
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
 * @since      Release 0.0.1dev6
 * @link       http://pear.php.net/package/Net_SMPP
 */
class Net_SMPP_Command_submit_sm extends Net_SMPP_Command
{
    /**
     * SMS application associated with this message
     *
     * Specifying the service_type allows the ESME to
     *  - avail of enhanced messaging services such as “replace by service”
     *    type
     *  - to control the teleservice used on the air interface.
     *
     * The following generic service_types are defined:
     *
     * ““ (NULL) Default
     * “CMT”     Cellular Messaging
     * “CPT”     Cellular Paging
     * “VMN”     Voice Mail Notification
     * “VMA”     Voice Mail Alerting
     * “WAP”     Wireless Application Protocol
     * “USSD”    Unstructured Supplementary Services Data
     *
     * Set to NULL for default SMSC settings.
     *
     * @var  string
     */
    var $service_type = null;

    /**
     * Type of Number for source address
     *
     * These fields define the Type of Number (TON) to be used in the SME
     * address parameters. The following TON values are defined:
     * TON                Value
     * Unknown            00000000 (0x00)
     * International      00000001 (0x01)
     * National           00000010 (0x02)
     * Network Specific   00000011 (0x03)
     * Subscriber Number  00000100 (0x04)
     * Alphanumeric       00000101 (0x05)
     * Abbreviated        00000110 (0x06)
     *
     * If not known, set to NULL.
     *
     * @var  int
     */
    var $source_addr_ton = NET_SMPP_TON_UNK;

    /**
     * Numbering Plan Indicator for source address
     *
     * These fields define the Numeric Plan Indicator (NPI) to be used in
     * the SME address parameters. The following NPI values are defined:
     *
     * NPI                      Value
     * Unknown                  00000000
     * ISDN (E163/E164)         00000001
     * Data (X.121)             00000011
     * Telex (F.69)             00000100
     * Land Mobile (E.212)      00000110
     * National                 00001000
     * Private                  00001001
     * ERMES                    00001010
     * Internet (IP)            00001110
     * WAP Client Id (to be     00010010
     *   defined by WAP Forum)
     *
     * If not known, set to NULL.
     *
     * @var  int
     */
    var $source_addr_npi = NET_SMPP_NPI_UNK;

    /**
     * Address of SME which originated this message.
     *
     * If not known, set to NULL
     */
    var $source_addr = null;

    /**
     * Type of Number for destination.
     *
     * @var  int
     */
    var $dest_addr_ton = NET_SMPP_TON_UNK;

    /**
     * Numbering Plan Indicator for destination.
     *
     * @var  int
     */
    var $dest_addr_npi = NET_SMPP_NPI_UNK;

    /**
     * Destination address of this short message.
     *
     * For mobile terminated messages, this is the directory number of the
     * recipient MS.
     *
     * @var  string
     */
    var $destination_addr;

    /**
     * Indicates Message Mode & Message Type.
     *
     * @var  int
     */
    var $esm_class;

    /**
     * Protocol Identifier.
     *
     * Network specific field.
     *
     * @var  int
     */
    var $protocol_id;

    /**
     * Designates the priority level of the message.
     *
     * @var  int
     */
    var $priority_flag;

     /**
      * The short message is to be scheduled by the SMSC for delivery.
      *
      * Set to NULL for immediate delivery
      *
      * @var  string
      */
    var $schedule_delivery_time = null;

    /**
     * The validity period of this message.
     *
     * Set to NULL to request the SMSC default validity period.
     *
     * @var  int
     */
    var $validity_period = null;

    /**
     * Indicator to signify if an SMSC delivery receipt or an SME
     * acknowledgement is required.
     *
     * @var  int
     */
    var $registered_delivery;


    /**
     * Flag indicating if submitted message should replace an existing message.
     *
     * @var  int
     */
    var $replace_if_present_flag = 0;

    /**
     * Defines the encoding scheme of the short message user data.
     *
     * @var  int
     */
    var $data_coding = NET_SMPP_ENCODING_DEFAULT;

    /**
     * Indicates the short message to send from a list of predefined (‘canned’)
     * short messages stored on the SMSC.
     *
     * If not using an SMSC canned message, set to NULL.
     *
     * @var  int
     */
    var $sm_default_msg_id = null;

    /**
     * Length in octets of the short_message user data.
     *
     * @var  int
     */
    var $sm_length = 0;

    /**
     * Up to 254 octets of short message user data.
     *
     * The exact physical limit for short_message size may vary according to
     * the underlying network.
     *
     * Applications which need to send messages longer than 254 octets should
     * use the message_payload parameter. In this case the sm_length field
     * should be set to zero.
     *
     * Note: The short message data should be inserted in either the
     * short_message or message_payload fields. Both fields must not be used
     * simultaneously.
     *
     * @var  string
     */
    var $short_message = null;

    // Optional
    var $user_message_reference;
    var $source_port;
    var $source_addr_subunit;
    var $destination_port;
    var $dest_addr_subunit;
    var $sar_msg_ref_num;
    var $sar_total_segments;
    var $sar_segment_seqnum;
    var $more_messages_to_send;
    var $payload_type;
    var $message_payload;
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
    var $ussd_service_op;

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
        'protocol_id' => array(
            'type' => 'int',
            'size' => 1
        ),
        'priority_flag' => array(
            'type' => 'int',
            'size' => 1
        ),
        'schedule_delivery_time' => array(
            'type' => 'string',
            'max' => 17
        ),
        'validity_period' => array(
            'type' => 'string',
            'max' => 17,
        ),
        'registered_delivery' => array(
            'type' => 'int',
            'size' => 1,
        ),
        'replace_if_present_flag' => array(
            'type' => 'int',
            'size' => 1
        ),
        'data_coding' => array(
            'type' => 'int',
            'size' => 1
        ),
        'sm_default_msg_id' => array(
            'type' => 'int',
            'size' => 1
        ),
        'sm_length' => array(
            'type' => 'int',
            'size' => 1
        ),
        'short_message' => array(
            'type' => 'ostring',
            'max' => 254,
            'lenField' => 'sm_length'
        ),
        // Optional params
        'user_message_reference' => array(
            'type' => 'int',
            'size' => 2
        ),
        'source_port' => array(
            'type' => 'int',
            'size' => 2
        ),
        'source_addr_subunit' => array(
            'type' => 'int',
            'size' => 1
        ),
        'destination_port' => array(
            'type' => 'int',
            'size' => 2
        ),
        'dest_addr_subunit' => array(
            'type' => 'int',
            'size' => 1
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
        'payload_type' => array(
            'type' => 'int',
            'size' => 1
        ),
        'message_payload' => array(
            'type' => 'ostring',
            'max' => 260
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
        ),
        'ussd_service_op' => array(
            'type' => 'int',
            'size' => 1
        )
    );

    /**
     * Prepare to generate the binary data
     *
     * This makes sure that only one of short_message/message_payload is used,
     * and calculates the value of sm_length.
     *
     * @return  void
     * @access  private
     */
    function prep()
    {
        if ($this->short_message !== null) {
            $this->sm_length = strlen($this->short_message);
            unset($this->message_payload);
        } else {
            $this->sm_length = 0;
        }
    }
}