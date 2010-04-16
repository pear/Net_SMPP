<?php

declare(ticks = 1);

require_once('SMPPLib.php');

/**
* Sends a recieved message somewhere
*
* Currently just prints message.
* 
* @param Net_SMPP::PDU $msgPDU the message received
*/
function consumeMsg($msgPDU)
{
    print("MSG FROM: $msgPDU->source_addr\n");
    print("MSG TO: $msgPDU->destination_addr\n");
    
    if ($msgPDU->command == "deliver_sm") {
        print("MSG: $msgPDU->short_message\n");
    }else if ($msgPDU->command == "data_sm") {
        print("MSG: $msgPDU->message_payload\n");
    }
}

$host = "127.0.0.1";
$port = 2775;
$pingCounter = 0;
$enquire_link_timer = 1000;
$authArgs = array("system_id"=>"loginName", "password"=>"password", "system_type"=>1);

$SMPP = new SMPPLib($host, $port);

while (true) {

    if ($SMPP->_state === NET_SMPP_CLIENT_STATE_CLOSED) {
        if (!$SMPP->connect()) {
            sleep(5);
        }
    }

    if ($SMPP->_state === NET_SMPP_CLIENT_STATE_OPEN) {
        $SMPP->authenticate($authArgs, SMPP_RECEIVER);
    }
        
    $packetRead =$SMPP->readPDU();
        
    if ($packetRead !== true && $packetRead !== false) {
        
        if ($packetRead->command == "deliver_sm" || $packetRead->command == "data_sm") {
            consumeMsg($packetRead);
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
