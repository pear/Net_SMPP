<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * SMPP v3.4 generic_nack command class and data
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
 * @since      Release 0.0.1dev2
 * @link       http://pear.php.net/package/Net_SMPP
 */

// Place includes, constant defines and $_GLOBAL settings here.
require_once 'Net/SMPP/Command.php';

/**
 * generic_nack class
 *
 * This is a generic negative acknowledgement to an SMPP PDU submitted with an
 * invalid message header. A generic_nack response is returned in the following
 * cases:
 *
 * - Invalid command_length
 *   If the receiving SMPP entity, on decoding an SMPP PDU, detects an invalid
 *   command_length (either too short or too long), it should assume that the data is corrupt. In
 *   such cases a generic_nack PDU must be returned to the message originator.
 *
 * - Unknown command_id
 *   If an unknown or invalid command_id is received, a generic_nack PDU must also be
 *   returned to the originator.
 *
 * This command has no paramaters
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
 * @since      Release 0.0.1dev2
 * @link       http://pear.php.net/package/Net_SMPP
 */
class Net_SMPP_Command_Generic_Nack extends Net_SMPP_Command
{
    /**
     * Paramater definitions for this command
     *
     * @var     array
     * @access  protected
     * @see     Net_SMPP_Command::$_defs
     */
    var $_defs = array();
}
