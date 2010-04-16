<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * SMPP v3.4 bind_receiver command class and data
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

// Constants for SMPP versions
define('NET_SMPP_VERSION_33', 0x33);
define('NET_SMPP_VERSION_34', 0x34);

/**
 * bind_receiver class
 *
 * An ESME bound as a Receiver is authorised to receive short messages from the SMSC and to
 * return the corresponding SMPP message responses to the SMSC.
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
class Net_SMPP_Command_Bind_Receiver extends Net_SMPP_Command
{
    /**
     * Identifies the ESME system requesting to bind as a receiver with the SMSC.
     *
     * The recommended use of system_id is to identify the binding entity, e.g.,
     * “InternetGW” in the case of an Internet Gateway or ‘VMS’ for a Voice
     * Mail System.
     *
     * @var  string
     */
    var $system_id;

    /**
     * The password may be used by the SMSC to authenticate the ESME requesting to bind.
     *
     * @var  string
     */
    var $password;

    /**
     * Identifies the type of ESME system requesting to bind as a transmitter with the SMSC.
     *
     * The system_type (optional) may be used to categorise the system, e.g.,
     * “EMAIL”, “WWW”, etc.
     *
     * @var  string
     */
    var $system_type;

    /**
     * Indicates the version of the SMPP protocol supported by the ESME.
     *
     * Possible values:
     * NET_SMPP_VERSION_33 (0x33): SMPP v3.3 or less
     * NET_SMPP_VERSION_34 (0x34): SMPP v3.4
     *
     * @var  int
     */
    var $interface_version = NET_SMPP_VERSION_34;

    /**
     * Indicates Type of Number of the ESME address.
     *
     * If not known set to NULL
     *
     * @var  int
     */
    var $addr_ton = null;

    /**
     * Numbering Plan Indicator for ESME address.
     *
     * If not known set to NULL.
     *
     * @var  int
     */
    var $addr_npi = null;

    /**
     * The ESME address.
     *
     * If not known set to NULL.
     *
     * @var  string
     */
    var $address_range = null;


    /**
     * Paramater definitions
     *
     * @var     array
     * @access  protected
     * @see     Net_SMPP_Command::$_defs
     */
    var $_defs = array(
        'system_id' => array(
            'type' => 'string',
            'max' => 16
        ),
        'password' => array(
            'type' => 'string',
            'max' => 9
        ),
        'system_type' => array(
            'type' => 'string',
            'max' => 13
        ),
        'interface_version' => array(
            'type' => 'int',
            'size' => 1
        ),
        'addr_ton' => array(
            'type' => 'int',
            'size' => 1
        ),
        'addr_npi' => array(
            'type' => 'int',
            'size' => 1
        ),
        'address_range' => array(
            'type' => 'string',
            'max' => 41
        )
    );
}
