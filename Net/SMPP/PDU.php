<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Net_SMPP PDU support
 *
 * This file contains the Net_SMPP_PDU class and various constants which are
 * needed for them to work.
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
 * @since      Release: 0.0.1dev1
 * @link       http://pear.php.net/package/Net_SMPP
 */

// Place includes, constant defines and $_GLOBAL settings here.

/**
 * SMPP error codes
 */
define('NET_SMPP_ESME_ROK',              0x00000000);
define('NET_SMPP_ESME_RINVMSGLEN',       0x00000001);
define('NET_SMPP_ESME_RINVCMDLEN',       0x00000002);
define('NET_SMPP_ESME_RINVCMDID',        0x00000003);
define('NET_SMPP_ESME_RINVBNDSTS',       0x00000004);
define('NET_SMPP_ESME_RALYBND',          0x00000005);
define('NET_SMPP_ESME_RINVPRTFLG',       0x00000006);
define('NET_SMPP_ESME_RINVREGDLVFLG',    0x00000007);
define('NET_SMPP_ESME_RSYSERR',          0x00000008);
define('NET_SMPP_ESME_RINVSRCADR',       0x0000000A);
define('NET_SMPP_ESME_RINVDSTADR',       0x0000000B);
define('NET_SMPP_ESME_RINVMSGID',        0x0000000C);
define('NET_SMPP_ESME_RBINDFAIL',        0x0000000D);
define('NET_SMPP_ESME_RINVPASWD',        0x0000000E);
define('NET_SMPP_ESME_RINVSYSID',        0x0000000F);
define('NET_SMPP_ESME_RCANCELFAIL',      0x00000011);
define('NET_SMPP_ESME_RREPLACEFAIL',     0x00000013);
define('NET_SMPP_ESME_RMSGQFUL',         0x00000014);
define('NET_SMPP_ESME_RINVSERTYP',       0x00000015);
define('NET_SMPP_ESME_RINVNUMDESTS',     0x00000033);
define('NET_SMPP_ESME_RINVDLNAME',       0x00000034);
define('NET_SMPP_ESME_RINVDESTFLAG',     0x00000040);
define('NET_SMPP_ESME_RINVSUBREP',       0x00000042);
define('NET_SMPP_ESME_RINVESMCLASS',     0x00000043);
define('NET_SMPP_ESME_RCNTSUBDL',        0x00000044);
define('NET_SMPP_ESME_RSUBMITFAIL',      0x00000045);
define('NET_SMPP_ESME_RINVSRCTON',       0x00000048);
define('NET_SMPP_ESME_RINVSRCNPI',       0x00000049);
define('NET_SMPP_ESME_RINVDSTTON',       0x00000050);
define('NET_SMPP_ESME_RINVDSTNPI',       0x00000051);
define('NET_SMPP_ESME_RINVSYSTYP',       0x00000053);
define('NET_SMPP_ESME_RINVREPFLAG',      0x00000054);
define('NET_SMPP_ESME_RINVNUMMSGS',      0x00000055);
define('NET_SMPP_ESME_RTHROTTLED',       0x00000058);
define('NET_SMPP_ESME_RINVSCHED',        0x00000061);
define('NET_SMPP_ESME_RINVEXPIRY',       0x00000062);
define('NET_SMPP_ESME_RINVDFTMSGID',     0x00000063);
define('NET_SMPP_ESME_RX_T_APPN',        0x00000064);
define('NET_SMPP_ESME_RX_P_APPN',        0x00000065);
define('NET_SMPP_ESME_RX_R_APPN',        0x00000066);
define('NET_SMPP_ESME_RQUERYFAIL',       0x00000067);
define('NET_SMPP_ESME_RINVOPTPARSTREAM', 0x000000C0);
define('NET_SMPP_ESME_ROPTPARNOTALLWD',  0x000000C1);
define('NET_SMPP_ESME_RINVPARLEN',       0x000000C2);
define('NET_SMPP_ESME_RMISSINGOPTPARAM', 0x000000C3);
define('NET_SMPP_ESME_RINVOPTPARAMVAL',  0x000000C4);
define('NET_SMPP_ESME_RDELIVERYFAILURE', 0x000000FE);
define('NET_SMPP_ESME_RUNKNOWNERR',      0x000000FF);


/**
 * Net_SMPP PDU (Protocol Data Unit) class
 *
 * This is the lowest-level class for handling PDUs, and it is responsible for
 * generating the PDU header, among other things.
 *
 * The design of this class is:
 *
 * Net_SMPP_Command_foobar
 *  -> Net_SMPP_Command
 *    -> Net_SMPP_PDU
 *
 * The Net_SMPP_Command_foobar class defines the paramaters which may be set
 * for any given command. Net_SMPP_Command has methods which operate on the
 * command definitions in Net_SMPP_Command_foobar, en/decode the binary
 * protocol data, and so forth.
 *
 * Simple example; send_sm command:
 * require_once 'Net/SMPP.php';
 * $ssm =& Net_SMPP::PDU('submit_sm');
 * $ssm->short_message = 'Testing';
 * // Generate the binary protocol data
 * $pdu = $ssm->generate();
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
 * @since      Release: 0.0.1dev1
 * @link       http://pear.php.net/package/Net_SMPP
 */
class Net_SMPP_PDU
{
    // Header
    /**
     * Octal length of the total PDU
     *
     * @var     int
     * @access  protected
     */
    var $_length = 0;

    /**
     * PDU command
     *
     * @var  int
     */
    var $command = null;

    /**
     * Status of the command
     *
     * This is only relevant for response PDUs
     *
     * @var  int
     */
    var $status = null;

    /**
     * PDU sequence
     *
     * @see  Net_SMPP::nextSeq()
     * @var  int
     */
    var $sequence = 0;


    /**
     * Get an instance of a specific command class
     *
     * @param   string  $command  Command class to instantiate
     * @param   array   $args     Arguments to set in the instance
     * @return  mixed   boolean false or Net_SMPP_Command instance
     */
    function &factory($command, $args = array()) {
        $class = 'Net_SMPP_Command_' . $command;
        $file = 'Net/SMPP/Command/' . $command . '.php';

        include_once $file;
        if (!class_exists($class)) {
            return false;
        }
        return new $class($command, $args);
    }

    /**
     * Is this a vendor PDU?
     *
     * @return  boolean  true if it is, false otherwise
     */
    function isVendor()
    {
        return isset($this->vendor);
    }

    /**
     * Is this a request PDU?
     *
     * @return  boolean  true if it is, false otherwise
     */
    function isRequest()
    {
        return ! $this->isResponse();
    }

    /**
     * Is this a response PDU?
     *
     * @return  boolean  true if it is, false otherwise
     */
    function isResponse()
    {
        //$code = $this->commandCode($this->command);
        if ($this->commandCode($this->command) & 0x80000000) {
            return true;
        }
        return false;
    }

    /**
     * Is this an error response?
     *
     * @return  boolean  true if it is, false otherwise
     */
    function isError()
    {
        if ($this->status != NET_SMPP_ESME_ROK) {
            return true;
        }
    }

    /**
     * Get status description
     *
     * @param   int     $status  Optional status code to look up
     * @return  string  Error message
     * @static  May be called statically if $status is set
     */
    function statusDesc($status = null)
    {
        static $descs;

        $st = is_null($status) ? $this->status : $status;

        if (!isset($descs)) {
            $descs =& Net_SMPP_PDU::_statusDescs();
        }

        if (isset($descs[$st])) {
            return $descs[$st];
        }

        return null;
    }

    /**
     * Parse a raw PDU and populate this instance with it's data
     *
     * This function only actually parses the (fixed-length) PDU header.
     * {@link parseParams()} handles the PDU-specific parameter parsing.
     *
     * @param   string  $pdudata  PDU data to parse
     * @see     extractCommand()
     * @see     parseParams()
     */
    function parse($pdudata)
    {
        /**
         * PDU Format:
         *
         * - Header (16 bytes)
         *   command_length  - 4 bytes
         *   command_id      - 4 bytes
         *   command_status  - 4 bytes
         *   sequence_number - 4 bytes
         * - Body (variable length)
         *   paramater
         *   paramater
         *   ...
         */
        $header = substr($pdudata, 0, 16);
        $this->_length = implode(null, $this->_unpack('N', substr($header, 0, 4)));
        $this->command = $this->extractCommand($pdudata);
        $this->status = implode(null, $this->_unpack('N', substr($header, 8, 4)));
        $this->sequence = implode(null, $this->_unpack('N',substr($header, 12, 4)));

        // Parse the rest.
        if (strlen($pdudata) > 16) {
            $this->parseParams(substr($pdudata, 16));
        }
        return true;
    }

    /**
     * unpack() signednedd kludge
     *
     * PHP & unpack() have problems with unsigned ints; if the high bit is set
     * in an unsigned int returned from unpack(), the int is treated as signed,
     * even if we requested that it be treated as unsigned.
     *
     * This function checks for the high bit, and if it's set, shifts it off and
     * sets it back with the & operator. It works around the problem on 32-bit
     * platforms, and doesn't break on 64-bit systems. It will probably break
     * in some cases on 8- or 16-bit, but we really don't care about those.
     *
     * @param   string  $format
     * @param   string  $data
     * @return  array
     * @link    http://atomized.org/2005/04/phps-integer-oddities/
     * @access  private
     */
    function _unpack($format, $data)
    {
        $val = array_values(unpack($format, $data));

        if ($format == 'N' && $val[0] < 0) {
            $val[0] = $val[0] << 1 >> 1;
            $val[0] += 0x80000000;
        }

        return $val;
    }

    /**
     * Extract the command from a PDU
     *
     * @param   string  $pdu  Binary PDU data
     * @return  string  PDU command string
     */
    function extractCommand($pdu)
    {
        $intcmd = Net_SMPP_PDU::_unpack('N', substr($pdu, 4, 4));
        $intcmd = $intcmd[0];

        return Net_SMPP_Command::commandName($intcmd);
    }

    /**
     * Generate the raw PDU to send to the remote system
     *
     * @return  string  PDU data
     */
    function generate()
    {
        $header = $body = '';

        // Generate the body
        $body = $this->generateParams();

        // Generate the header
        $this->_length = strlen($body) + 16;
        $ha['length'] = pack('N', $this->_length);
        $ha['command'] = pack('N', $this->commandCode($this->command));
        $ha['status'] = pack('N', $this->status);
        $ha['sequence'] =  pack('N', $this->sequence);
        $header = $ha['length'] . $ha['command'] . $ha['status'] . $ha['sequence'];

        return $header . $body;
    }

    /**
     * SMPP error descriptions
     *
     * @return  array
     * @access  private
     * @static
     */
    function &_statusDescs()
    {
        static $descs = array(
            NET_SMPP_ESME_ROK              => 'No Error',
            NET_SMPP_ESME_RINVMSGLEN       => 'Message Length is invalid',
            NET_SMPP_ESME_RINVCMDLEN       => 'Command Length is invalid',
            NET_SMPP_ESME_RINVCMDID        => 'Invalid Command ID',
            NET_SMPP_ESME_RINVBNDSTS       => 'Incorrect BIND Status for given command',
            NET_SMPP_ESME_RALYBND          => 'ESME Already in Bound State',
            NET_SMPP_ESME_RINVPRTFLG       => 'Invalid Priority Flag',
            NET_SMPP_ESME_RSYSERR          => 'System Error',
            NET_SMPP_ESME_RINVSRCADR       => 'Invalid Source Address',
            NET_SMPP_ESME_RINVDSTADR       => 'Invalid Dest Addr',
            NET_SMPP_ESME_RINVMSGID        => 'Message ID is invalid',
            NET_SMPP_ESME_RBINDFAIL        => 'Bind Failed',
            NET_SMPP_ESME_RINVPASWD        => 'Invalid Password',
            NET_SMPP_ESME_RINVSYSID        => 'Invalid System ID',
            NET_SMPP_ESME_RCANCELFAIL      => 'Cancel SM Failed',
            NET_SMPP_ESME_RREPLACEFAIL     => 'Replace SM Failed',
            NET_SMPP_ESME_RMSGQFUL         => 'Message Queue Full',
            NET_SMPP_ESME_RINVSERTYP       => 'Invalid Service Type',
            NET_SMPP_ESME_RINVNUMDESTS     => 'Invalid number of destinations',
            NET_SMPP_ESME_RINVDLNAME       => 'Invalid Distribution List name',
            NET_SMPP_ESME_RINVDESTFLAG     => 'Destination flag is invalid (submit_multi)',
            NET_SMPP_ESME_RINVSUBREP       => 'Invalid ‘submit with replace’ request (i.e. submit_sm with replace_if_present_flag set)',
            NET_SMPP_ESME_RINVESMCLASS     => 'Invalid esm_class field data',
            NET_SMPP_ESME_RCNTSUBDL        => 'Cannot Submit to Distribution List',
            NET_SMPP_ESME_RSUBMITFAIL      => 'submit_sm or submit_multi failed',
            NET_SMPP_ESME_RINVSRCTON       => 'Invalid Source address TON',
            NET_SMPP_ESME_RINVSRCNPI       => 'Invalid Source address NPI',
            NET_SMPP_ESME_RINVDSTTON       => 'Invalid Destination address TON',
            NET_SMPP_ESME_RINVDSTNPI       => 'Invalid Destination address NPI',
            NET_SMPP_ESME_RINVSYSTYP       => 'Invalid system_type field',
            NET_SMPP_ESME_RINVREPFLAG      => 'Invalid replace_if_present flag',
            NET_SMPP_ESME_RINVNUMMSGS      => 'Invalid number of messages',
            NET_SMPP_ESME_RTHROTTLED       => 'Throttling error (ESME has exceeded allowed message limits)',
            NET_SMPP_ESME_RINVSCHED        => 'Invalid Scheduled Delivery Time',
            NET_SMPP_ESME_RINVEXPIRY       => 'Invalid message validity  period (Expiry time)',
            NET_SMPP_ESME_RINVDFTMSGID     => 'Predefined Message Invalid or Not Found',
            NET_SMPP_ESME_RX_T_APPN        => 'ESME Receiver Temporary App Error Code',
            NET_SMPP_ESME_RX_P_APPN        => 'ESME Receiver Permanent App Error Code',
            NET_SMPP_ESME_RX_R_APPN        => 'ESME Receiver Reject Message Error Code',
            NET_SMPP_ESME_RQUERYFAIL       => 'query_sm request failed',
            NET_SMPP_ESME_RINVOPTPARSTREAM => 'Error in the optional part of the PDU Body.',
            NET_SMPP_ESME_ROPTPARNOTALLWD  => 'Optional Parameter not allowed',
            NET_SMPP_ESME_RINVPARLEN       => 'Invalid Parameter Length.',
            NET_SMPP_ESME_RMISSINGOPTPARAM => 'Expected Optional Parameter missing',
            NET_SMPP_ESME_RINVOPTPARAMVAL  => 'Invalid Optional Parameter Value',
            NET_SMPP_ESME_RDELIVERYFAILURE => 'Delivery Failure (used for data_sm_resp)',
            NET_SMPP_ESME_RUNKNOWNERR      => 'Unknown Error'
        );

        return $descs;
    }
}
?>