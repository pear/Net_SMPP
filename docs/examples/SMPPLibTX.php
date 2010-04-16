<?php

declare(ticks = 1);

require_once('SMPPLib.php');

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
    print(memory_get_usage()."\n");
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
