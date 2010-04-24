<?php

ini_set('include_path', '/home/silo/Projects/PEAR/SMPP/trunk:/usr/share/php:/usr/share/pear');

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Net_SMPP example
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
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @version    CVS:     $Revision$
 * @since      Release 0.4.3
 * @link       http://pear.php.net/package/Net_SMPP
 */

require_once 'Net/SMPP.php';

// Get a new bind_transmitter PDU
$bt &= Net_SMPP::PDU('bind_transmitter');
// Set some paramaters
$bt->system_id = 'my_systemid';
$bt->password  = 'my_password';
// Generate the binary data
$bt_data =& $bt->generate();

$ssm =& Net_SMPP::PDU('submit_sm');
$ssm->source_addr_ton  = NET_SMPP_TON_NWSPEC;        // Network-specific (i.e. shortcode)
$ssm->source_addr      = 123456;
$ssm->dest_addr_ton    = NET_SMPP_TON_INTL;          // International
$ssm->destination_addr = '15555551212';
$ssm->data_coding      = NET_SMPP_ENCODING_ISO88591; // Latin-1
$ssm->short_message    = 'Greetings, humans!';
$ssm_data =& $ssm->generate();

// Parse binary PDU data into the appropriate class
$_ssm =& Net_SMPP::parsePDU($ssm_data);
$_bt  =& Net_SMPP::parsePDU($bt_data);

?>
