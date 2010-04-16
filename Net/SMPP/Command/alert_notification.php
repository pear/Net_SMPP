<?php
/**
 * SMPP v3.4 alert_notification command class and/or data
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
 * @author     Silospen <silospen@silospen.com>
 * @copyright  Portions of the documentation (c) Copyright 1999 SMPP Developers
 *             Forum.
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    SVN: $Id$
 * @since      Release
 * @link       http://pear.php.net/package/Net_SMPP
 */

// Place includes, constant defines and $_GLOBAL settings here.
require_once 'Net/SMPP/Command.php';

/**
 * alert_notification class
 *
 * This message is sent by the SMSC to the ESME, when the SMSC has detected that a particular
 * mobile subscriber has become available and a delivery pending flag had been set for that
 * subscriber from a previous data_sm operation.
 * It may be used for example to trigger a data content ‘Push’ to the subscriber from a WAP Proxy
 * Server.
 * 
 *
 * @category   Networking
 * @package    Net_SMPP
 * @author     Silospen <silospen@silospen.com>
 * @copyright  Portions of the documentation (c) Copyright 1999 SMPP Developers
 *             Forum.
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @since      Release
 * @link       http://pear.php.net/package/Net_SMPP
 */
class Net_SMPP_Command_Alert_Notification extends Net_SMPP_Command
{
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
     * Type of Number for emse.
     *
     * @var  int
     */
    var $esme_addr_ton = NET_SMPP_TON_UNK;

    /**
     * Numbering Plan Indicator for esme.
     *
     * @var  int
     */
    var $esme_addr_npi = NET_SMPP_NPI_UNK;

    /**
     * Specifies the address of an ESME address to which an alert_notification should be routed.
     *
     * @var  string
     */
    var $esme_addr;

    // Optional
    var $ms_availability_status;

    /**
     * Paramater definitions for this command
     *
     * @var     array
     * @access  protected
     * @see     Net_SMPP_Command::$_defs
     */
    var $_defs = array(
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
        'esme_addr_ton' => array(
            'type' => 'int',
            'size' => 1,
        ),
        'esme_addr_npi' => array(
            'type' => 'int',
            'size' => 1,
        ),
        'esme_addr' => array(
            'type' => 'string',
            'max' => 21
        ),
        // Optional params
        'ms_availability_status' => array(
            'type' => 'int',
            'size' => 1
        )
    );
}  
?>
