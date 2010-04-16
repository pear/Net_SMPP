<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Net_SMPP - main file
 *
 * Net_SMPP is an implementation of the SMPP v3.3 and v3.4 protocols.
 * Portions of the documentation are reproduced with permission from the
 * SMPP v3.4 Specification, Issue 1.2, (c) 1999 SMPP Developers Forum.
 *
 * This document is essential reading to make use of Net_SMPP, and may be
 * freely downloaded from {@link http://smsforum.net/doc/download.php?id=smppv34}
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
 * @link       http://pear.php.net/package/Net_SMPP
 */

// Place includes, constant defines and $_GLOBAL settings here.

define('MAX_SEQ', 2147483646);

/**
 * Main Net_SMPP class
 *
 * This class contains a few methods for handling top-level SMPP actions.
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
 * @link       http://pear.php.net/package/Net_SMPP
 * @static
 */
class Net_SMPP
{
    /**
     * Get the next sequence number
     *
     * @return  int        Next sequence
     * @access  protected
     * @static  $sequence  Last used sequence number
     */
    function nextSeq()
    {
        static $sequence;
        if (!isset($sequence)) {
            $sequence = 0;
        }
        
        if ($sequence == MAX_SEQ) {
            $sequence = 0;
        }
        
        return ++$sequence;
    }

    /**
     * Get an instance of a PDU class
     *
     * @param   string  $command  Command class
     * @return  mixed   Net_SMPP_PDU::factory()'s return value
     * @see     Net_SMPP_PDU::factory()
     * @static
     */
    function PDU($command, $args = array())
    {
        require_once 'Net/SMPP/PDU.php';
        return Net_SMPP_PDU::factory($command, $args);
    }

    /**
     * Parse a binary PDU
     *
     * @param   string  $data  Raw (binary) PDU data
     * @return  object  Net_SMPP_Command_* instance
     */
    function &parsePDU($data)
    {
        require_once 'Net/SMPP/PDU.php';
        $command = Net_SMPP_PDU::extractCommand($data);
        if ($command === false) {
            return false;
        }
        $pdu =& Net_SMPP::PDU($command, array('sequence' => 'dummy'));
        $pdu->parse($data);
        return $pdu;
    }
}
?>
