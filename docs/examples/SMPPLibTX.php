<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * SMPP v3.4 SMPPLibTX script
 *
 * PHP versions 5
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
declare(ticks = 1);

require_once('SMPPLib.php');

/**
 * SMPPLibTX script
 *
 * This shows an example of using SMPPLib and Net_SMPP as a transmitter
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


/**
* Gets a message from somewhere
*
* Currently just creates the same message.
* 
* @return mixed array of message arguements
*/
function produceMsg()
{
    $msgArgs = array(
    'source_addr' => 123456789,
    'destination_addr' => 123456789,
    'short_message' => 'Want to go to the cinema?');
    return $msgArgs;
}

$host = "127.0.0.1";
$port = 2775;
$pingCounter = 0;
$enquire_link_timer = 1000;
$authArgs = array("system_id"=>"loginName", "password"=>"password", "system_type"=>1);


$SMPP = new SMPPLib($host, $port);

while (true) {
    if ($SMPP->_state === NET_SMPP_CLIENT_STATE_CLOSED) {
        if(!$SMPP->connect()){
            sleep(5);
        }
    }

    if ($SMPP->_state === NET_SMPP_CLIENT_STATE_OPEN) {
        $SMPP->authenticate($authArgs, SMPP_TRANSMITTER);
    }
    
    $SMPP->readPDU();
    
    if ($SMPP->_state === NET_SMPP_CLIENT_STATE_BOUND_TX && !$SMPP->sendStackFull()) {
   
        $msgArgs = produceMsg();
        
        if (count($msgArgs) > 0) {
            $packetSent = $SMPP->sendMsg($msgArgs);
        }
    }
        
    if ($pingCounter >= $enquire_link_timer && !$SMPP->sendStackFull()) {
        //ping to keep connection alive.
        $SMPP->sendPing();
        $pingCounter = 0;
    }
    $pingCounter++;
        
    time_nanosleep(0,TEN_MSECONDS);
}

$SMPP->deauthenticate($authArgs);
$SMPP->disconnect();

?>
