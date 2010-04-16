<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * SMPP v3.4 enquire_link command class and/or data
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
 * enquire_link class
 *
 * This message can be sent by either the ESME or SMSC and is used to provide a confidence-
 * check of the communication path between an ESME and an SMSC. On receipt of this request
 * the receiving party should respond with an enquire_link_resp, thus verifying that the
 * application level connection between the SMSC and the ESME is functioning. The ESME may
 * also respond by sending any valid SMPP primitive.
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
class Net_SMPP_Command_Enquire_Link extends Net_SMPP_Command
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
