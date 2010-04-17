<?php


/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * SMPP v3.4 SMPPLib class
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
error_reporting(E_ALL);
require_once 'Net/Socket.php'; 
require_once 'Net/SMPP.php';

declare(ticks = 1);

/**
 * Time constants in nanoseconds.
 */
define('TEN_MSECONDS', 10000000);

/**
 * Some connection state constants
 */
define('NET_SMPP_CLIENT_STATE_CLOSED', 0);
define('NET_SMPP_CLIENT_STATE_OPEN', 1);
define('SMPPLIB_STATE_AUTH_SENT', 2);
define('NET_SMPP_CLIENT_STATE_BOUND_TX', 3);
define('NET_SMPP_CLIENT_STATE_BOUND_RX', 4);

/**
 * Debug levels
 */
define('SMPP_LOG_DEBUG', 0);
define('SMPP_LOG_INFO', 1);
define('SMPP_LOG_ERROR', 2);
define('SMPP_LOG_CRITICAL', 3);

/**
 * Possible authentication states
 */
define('SMPP_TRANSMITTER', 'bind_transmitter');
define('SMPP_RECEIVER', 'bind_receiver');

/**
 * The size of the sending stack, defined in the SMPP docs
 */
define('MAX_STACK_SIZE', 10);

/**
 * SMPPLib class
 *
 * This is an example of a client set up to use Net_SMPP.
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


class SMPPLib
{

    /**
     * Log will only display above this debug level
     * @var int
     */
    var $_debugLevel = SMPP_LOG_INFO;

    /**
     * Host IP address
     * @var string 
     */
    var $_host = null;
    
    /**
     * Port number
     * @var int
     */
    var $_port = null;
    
    /**
     * Socket to connect over
     * @var Net_Socket
     */
    var $_socket = null;

    /**
     * The sending stack of messages waiting to be ACKnowledged
     * @var int => Net_SMPP::PDU (PDU seq => PDU obj)
     */
    var $_pduStack = array();
    
    /**
     * Current connection state
     * @var int
     */
    var $_state = NET_SMPP_CLIENT_STATE_CLOSED;

    /**
     * Maintains count of how many times a packet has been retransmitted
     * @var int => int (PDU seq => num times retried)
     */
    var $_retryCounter = array();
    
    /**
     * List of child times spawned off from the main process
     * @var int => int (PDU seq => child process PID)
     */
    var $_childPIDList = array();

    /**
     * Time in seconds to wait before retrying a PDU send
     * @var int
     */
    var $_response_timer = 5;
    
    /**
     * Number of time to retry a failed PDU before reconnecting
     * @var int
     */
    var $_retryNumber = 5;

    /**
     * Class constructor
     * 
     * Sets the connection variables
     *
     * @param string $host the ip address to connect to
     * @param int    $port the port to connect to
     *
     * @return void
     */
    function __construct($host, $port)
    {
        $this->_host = $host;
        $this->_port = intval($port);
        $this->_socket = new Net_Socket;
        $this->initHandler();
    }

    /**
     * Sets up the signal handler
     * 
     * @return void
     */
    function initHandler()
    {
        pcntl_signal(SIGCHLD, array(&$this,"timerFinishedHandler"));
    }

    /**
     * Catches a retry timer timing out
     * 
     * Is called when a child process exits correctly, after the retry timer has
     * timed out. Retries send or connection depending on situation
     *
     * @param int $signal the signal caught
     *
     * @return void
     */
    function timerFinishedHandler($signal)
    {
        switch ($signal) {
        /* SIGCHLD is sent when a child terminates. The child dies and becomes a
           zombie process, state Z+. It waits for the parent process to reap it
        */
        case SIGCHLD:
            
            /*  PCNTL_WAIT waits until a child has terminated. If it has, it reaps
                the zombie process and decides what to do. This will loop over all
                the currently zombified children and reap them, dealing with
                retransmission
            */
            
            while (($childPID = pcntl_wait($pduSeq, WNOHANG)) > 0) {
            
                /* This checks to see if it exited properly. If a child exits
                   properly, that means the timer quit after a timeout, so we need
                   to retransmit. If the child didn't exit properly, that means the
                   process was killed with SIGTERM, so the PDU ACK was received.
                */
                
                if (!pcntl_wifexited($pduSeq)) {
                    $this->log(
                        SMPP_LOG_DEBUG, "DEBUG: Timer $childPID quit correctly"
                    );
                    continue;
                }
                $pduSeq = array_search($childPID, $this->_childPIDList);
                
                /* This is an error, but we're not sure how to handle it. A timer
                   has finished and is alerting that the ACK hasn't been recieved,
                   but the PDU isn't in the send stack. So an ACK must have been
                   receieved.
                */
                
                if ($pduSeq === false) {
                    continue;
                }

                $this->log(
                    SMPP_LOG_ERROR, "ERR: PDU ".$pduSeq." failed. Retrying.\n"
                );

                if (!array_key_exists($pduSeq, $this->_retryCounter)) {
                    $this->_retryCounter[$pduSeq] = 1;
                } else {
                    $this->_retryCounter[$pduSeq]++;
                }

                if ($this->_retryCounter[$pduSeq] > $this->_retryNumber) {
                    $this->log(
                        SMPP_LOG_CRITICAL, 
                        "CRITICAL: Retried PDU $pduSeq $this->_retryNumber times!\n"
                    );
                    $this->disconnect();
                    $this->resetRetryCounter();
                }

                //Ensures that the system is in the correct state to attempt retry
                if ($this->_state === NET_SMPP_CLIENT_STATE_BOUND_TX
                    || $this->_state === SMPPLIB_STATE_AUTH_SENT
                    || $this->_state === NET_SMPP_CLIENT_STATE_BOUND_RX
                ) {

                    $this->sendPDU($this->_pduStack[$pduSeq]);
                }else{
                    //We want the PDUs to keep trying to be sent while
                    // reconnecting. If they cannot be sent, they sleep
                    // and try again after the timeout.
                    $this->resetRetryCounter();
                    $this->startTimer($pduSeq);
                }
            }
        }
    }

    /**
     * Kills all spawned time processes
     * 
     * Not currently used
     *
     * @return void
     */
    function killChildren()
    {
        while ($child = array_pop($this->_childPIDList)) {
            posix_kill($child, SIGTERM);
            //reaps the child process - might not be neccessary
            pcntl_waitpid($child, $status, WNOHANG);
        }
    }

    /**
     * Connects to the SMSC
     * 
     * @return bool success/faliure of connection
     */
    function connect()
    {
        $this->log(
            SMPP_LOG_INFO, 'INFO: Connecting to ' . $this->_host . ':' . $this->_port
        );
        
        $res =& $this->_socket->connect($this->_host, $this->_port);
        $this->_socket->setBlocking(false);

        if (!PEAR::isError($res)) {
            $this->_state = NET_SMPP_CLIENT_STATE_OPEN;
            $this->log(SMPP_LOG_INFO, "INFO: Connected");
            return true;
        }

        $this->log(SMPP_LOG_ERROR, "ERR: Connection Failed");
        return false;
    } 

    /**
     * Disconnects from the SMSC
     * 
     * @return bool success/faliure of connection
     */
    function disconnect()
    {
        $res =& $this->_socket->disconnect();
        
        if (!PEAR::isError($res)) {
            $this->_state = NET_SMPP_CLIENT_STATE_CLOSED;
            $this->log(SMPP_LOG_INFO, "INFO: Disconnected");
            return true;
        }
        
        return false; 
    }

    /**
     * Authenticates with the SMSC
     * 
     * Sends a bind_transmitter/receiver PDU
     *
     * @param mixed $authArgs authentication details (user/password/etc)
     * @param int   $esmeType type of PDU to send
     *
     * @return bool success/faliure of send
     */
    function authenticate($authArgs, $esmeType)
    {
        if ($this->_state !== NET_SMPP_CLIENT_STATE_OPEN) {
            $this->log(SMPP_LOG_ERROR, "ERR: Wrong state to authenticate");
            return false;
        }

        $pdu =& Net_SMPP::PDU($esmeType, $authArgs);
        $res =& $this->sendPDU($pdu);
        
        if ($res === false ) {
            $this->log(SMPP_LOG_ERROR, "ERR: Authentication failed");
            return false;
        }
        
        $this->_state = SMPPLIB_STATE_AUTH_SENT;
        return true;
    }
    
    /**
     * Deauthenticates with SMSC
     * 
     * Sends an unbind PDU
     *
     * @return bool success/faliure of send
     */
    function deauthenticate()
    {
        if ($this->_state !== NET_SMPP_CLIENT_STATE_BOUND_TX) {
            $this->log(SMPP_LOG_ERROR, "ERR: Wrong state to deauthenticate");
            return false;
        }

        $pdu =& Net_SMPP::PDU('unbind');
        $res =& $this->sendPDU($pdu);
        
        if ($res === false) {
            $this->log(SMPP_LOG_ERROR, "ERR: Deauthentication failed");
            return false;
        }
        
        $this->_state = NET_SMPP_CLIENT_STATE_OPEN;
        return true;
    }

    /**
     * Sends an enquire_link 'ping' PDU
     * 
     * Pinging keeps the connection alive
     *
     * @return bool success/faliure of ping
     */
    function sendPing()
    {
        if ($this->_state !== NET_SMPP_CLIENT_STATE_BOUND_TX 
            && $this->_state !== NET_SMPP_CLIENT_STATE_BOUND_RX
        ) {
            $this->log(SMPP_LOG_ERROR, "ERR: Wrong state to ping");
            return false;
        }

        $pdu =& Net_SMPP::PDU('enquire_link');
        $res = $this->sendPDU($pdu);
        
        if ($res === false ) {
            $this->log(SMPP_LOG_ERROR, "ERR: Authentication failed");
            return false;
        }
        return true;
    }

    /**
     * Send a SMS message
     * 
     * Sends a submit_sm PDU
     *
     * @param mixed $msgArgs PDU arguments (addresses/message/etc)
     *
     * @return mixed false on send fail, int message sequence number on success
     */
    function sendMsg($msgArgs)
    {
        if ($this->_state !== NET_SMPP_CLIENT_STATE_BOUND_TX) {
            $this->log(SMPP_LOG_ERROR, "ERR: Wrong state to send message");
            return false;
        }

        $pdu =& Net_SMPP::PDU('submit_sm', $msgArgs);
        $res =& $this->sendPDU($pdu);
        
        if ($res === false ) {
            $this->log(SMPP_LOG_ERROR, "ERR: Send message failed");
            return false;
        }
        return $res;
    }

    /**
     * Send a PDU down a socket
     * 
     * Generates and sends a PDU. Pushes it onto the send stack to wait for ACK.
     * Responses do not need to be ACK'd.
     *
     * @param NET_SMPP::PDU &$pdu PDU packet to be sent
     *
     * @return int PDU sequence number
     */
    function sendPDU(&$pdu)
    {
        if ($pdu->isRequest()) {
            $this->pushPDU($pdu);
        }

        $res =& $this->_socket->write($pdu->generate());
        
        if (PEAR::isError($res)) {
            return false;
        }
        
        $this->log(
            SMPP_LOG_INFO, 'INFO: Sent ' . $pdu->command . ' PDU ' . $pdu->sequence
        );

        if ($pdu->isRequest()) {
            $this->startTimer($pdu->sequence);
        }

        return $pdu->sequence;
    }

    /**
     * Starts a retry timer
     * 
     * Forks off a child to count to $timeout and exit. Adds childs PID to PID list
     *
     * @param int $seqNum the PDU sequence number this child belongs to
     *
     * @return void
     */
    function startTimer($seqNum)
    {
    
        $timeout = $this->_response_timer;
        $pid = pcntl_fork();

        if ($pid == -1) {
            die("ERR: Error forking timer.");
        } else if ($pid == 0) {
            //THIS IS THE CHILD
            $this->log(
                SMPP_LOG_DEBUG, "DEBUG: Timer started for PDU ID: " . $seqNum . "\n"
            );
            sleep($timeout);
            exit(0);
        } else if ($pid > 0) {
            //THIS IS THE PARENT
            $this->_childPIDList[$seqNum] = $pid;
        }
    }

    /**
     * Places a PDU on the stack, waiting for an ACK
     * 
     * @param NET_SMPP::PDU &$pdu the packet which is waiting for an ACK
     *
     * @return void
     */
    function pushPDU(&$pdu)
    {
        assert($pdu->isRequest());
        $this->_pduStack[$pdu->sequence] =& $pdu;
    }

    /**
     * Takes a PDU which has being ACK'd off the stack
     * 
     * This function also kills (SIGTERM) any child timer who is waiting for the ACK
     *
     * @param NET_SMPP::PDU &$pdu the ACK recieved
     *
     * @return bool success/faliure of pop
     */
    function popPDU(&$pdu)
    {
        assert(!$pdu->isRequest());
        
        //If the PDU is waiting for ACK
        if (array_key_exists($pdu->sequence, $this->_pduStack)) {

            //If the child retry timer is still alive
            if (array_key_exists($pdu->sequence, $this->_childPIDList)) {
                
                //Stop the timer, as ACK has been received
                posix_kill($this->_childPIDList[$pdu->sequence], SIGTERM);
                pcntl_waitpid($this->_childPIDList[$pdu->sequence], $state, WNOHANG);
                unset($this->_childPIDList[$pdu->sequence]);
                   
                //If the packet has been retried
                if (array_key_exists($pdu->sequence, $this->_retryCounter)) {
                    unset($this->_retryCounter[$pdu->sequence]);
                }
                
            }
            
            unset($this->_pduStack[$pdu->sequence]);
            return true;
        }
        //Not sure how to handle this situation. Assume all is OK?
        $this->log(
            SMPP_LOG_ERROR, 
            "ERR: Response ".$pdu->sequence." to no request, but that's ok."
        );
        
        return false;
    }

    /**
     * Logs a message
     * 
     * @param int    $debugLevel the level of debugging this message is at
     * @param string $msg        the message to log
     *
     * @return void
     */
    function log($debugLevel, $msg)
    {
        if ($debugLevel >= $this->_debugLevel) {
            print("$msg\n");
        }
    }

    /**
     * Reads a PDU from the socket
     * 
     * If there is a PDU on the socket, it is read. Standard requests are responded
     * to with ACKs. Standard responses are also dealt with. 
     *
     * @return mixed bool on success/faliure, NET_SMPP::PDU if this is a request
     * message, as this could be a deliver_sm
     */
    function readPDU()
    {
    
        if ($this->_state === NET_SMPP_CLIENT_STATE_CLOSED) {
            $this->log(SMPP_LOG_ERROR, "ERR: Wrong state to read PDU");
            return false;
        }
        $rawlen = $this->_socket->read(4);

        if (PEAR::isError($rawlen)) {
            $this->log(SMPP_LOG_ERROR, 'ERR: Error reading PDU length');
            return false;
        } else if (strlen($rawlen) === 0) {
            $this->log(SMPP_LOG_DEBUG, 'DEBUG: No data to read');
            return false;
        }
 
        $len = array_values(unpack('N', $rawlen));
        $len = $len[0];

        $rawpdu = $this->_socket->read($len - 4);
        
        if (PEAR::isError($rawpdu)) {
            $this->log(SMPP_LOG_ERROR, 'ERR: Error reading PDU data');
            return false;
        }

        $rawpdu = $rawlen . $rawpdu;
        $cmd = Net_SMPP_PDU::extractCommand($rawpdu);
        
        if ($cmd === false) {
            $this->log(SMPP_LOG_ERROR, 'ERR: Error bad PDU received');
            return;
        }
        
        $pdu =& Net_SMPP::parsePDU($rawpdu);
        $this->log(
            SMPP_LOG_INFO, 'INFO: Recieved ' . $cmd . ' PDU ' . $pdu->sequence
        );
 
        if ($pdu->isRequest()) {
            $this->handleRequest($pdu, $cmd);
            return $pdu;
        } else {
            $this->handleResponse($pdu, $cmd);
            return true;
        }
    }

    /**
     * Automatically deals with recieved response PDUs
     * 
     * Sets the connection state if this is necessary
     *
     * @param NET_SMPP::PDU &$resPdu response PDU recieved
     * @param int           $cmd     command_id of response PDU received
     *
     * @return void
     */
    function handleResponse(&$resPdu, $cmd)
    {
        //Take PDU waiting for ACK off the stack
        if ($cmd !== 'generic_nack') {
            $this->popPDU($resPdu);
        }
        
        if ($resPdu->isError()) {
            $this->log(
                SMPP_LOG_ERROR, 
                'ERR: Response PDU was an error: ' . $resPdu->statusDesc()
            );
        }

        if ($cmd === 'bind_transmitter_resp') {
        
            if ($resPdu->isError()) {
                $this->_state = NET_SMPP_CLIENT_STATE_OPEN;
                return;
            } else {
                $this->_state = NET_SMPP_CLIENT_STATE_BOUND_TX;
                $this->log(SMPP_LOG_INFO, "INFO: Authenticated");
                return;
            }
            
        }

        if ($cmd === 'bind_receiver_resp') {
        
            if ($resPdu->isError()) {
                $this->_state = NET_SMPP_CLIENT_STATE_OPEN;
                return;
            } else {
                $this->_state = NET_SMPP_CLIENT_STATE_BOUND_RX;
                $this->log(SMPP_LOG_INFO, "INFO: Authenticated");
                return;
            }
            
        }

        if ($cmd === 'unbind_resp') {
            $this->_state = NET_SMPP_CLIENT_STATE_OPEN;
            $this->log(SMPP_LOG_INFO, "INFO: Deauthenticated");
            return;
        }
        if ($cmd === 'enquire_link_resp') {
            $this->log(SMPP_LOG_INFO, "INFO: Pinged");
            return;
        }
        return;
    }

    /**
     * Automatically deals with recieved request PDUs
     * 
     * Sets the connection state if this is necessary
     *
     * @param NET_SMPP::PDU &$reqPdu request PDU recieved
     * @param int           $cmd     command_id of request PDU received
     *
     * @return void
     */
    function handleRequest(&$reqPdu, $cmd)
    {
        if ($cmd === 'unbind') {
        
            $pdu =& Net_SMPP::PDU(
                'unbind_resp', array('sequence'=>$reqPdu->sequence)
            );
            
            $res =& $this->sendPDU($pdu);
            
            if ($res === false ) {
                $this->log(SMPP_LOG_ERROR, "ERR: Unbind failed");
            }
            
            $this->_state = NET_SMPP_CLIENT_STATE_OPEN;
            $this->log(SMPP_LOG_INFO, "INFO: Server deauthenticated");
            return;
            
        } else if ($cmd === 'enquire_link') {
        
            $pdu =& Net_SMPP::PDU(
                'enquire_link_resp', array('sequence'=>$reqPdu->sequence)
            );
                
            $res =& $this->sendPDU($pdu);
            
            if ($res === false ) {
                $this->log(SMPP_LOG_ERROR, "ERR: Pong failed");
            }
            
            $this->log(SMPP_LOG_INFO, "INFO: Ponged");
            return;
            
        } else if ($cmd === 'deliver_sm') {
        
            $pdu =& Net_SMPP::PDU(
                'deliver_sm_resp', array('sequence'=>$reqPdu->sequence)
            );
            
            $res =& $this->sendPDU($pdu);
            
            if ($res === false ) {
                $this->log(SMPP_LOG_ERROR, "ERR: deliver_sm_resp failed");
            }
            
            $this->log(SMPP_LOG_INFO, "INFO: Responded to deliver_sm");
            return;
            
        } else if ($cmd === 'data_sm') {
        
            $pdu =& Net_SMPP::PDU(
                'data_sm_resp', array('sequence'=>$reqPdu->sequence)
            );
            
            $res =& $this->sendPDU($pdu);
            
            if ($res === false ) {
                $this->log(SMPP_LOG_ERROR, "ERR: data_sm_resp failed");
            }
            
            $this->log(SMPP_LOG_INFO, "INFO: Responded to data_sm");
            return;
        }
    }

    /**
     * Logs the current state of the sending stack
     * 
     * @return void
     */
    function printSendStack()
    {
        $this->log(SMPP_LOG_INFO, "DEBUG: STACKDUMP:\n");
        
        foreach ($this->_pduStack as $key => $value) {
            $this->log(SMPP_LOG_INFO, "DEBUG: ".$key. " : " .$value->command."\n");
        }
    }
    
    /**
     * Resets the state of SMPP_Lib in case of a reconnect command
     * 
     * Empties the pdu send stack, the retransmission counter and childPID list
     *
     * @return void
     */
    function resetRetryCounter()
    {
        $this->_retryCounter = array();
    }

    /**
     * Checks to see if an ACK has been received for a PDU
     * 
     * Takes the PDU seq number and checks to see if it is on the PDU send stack
     *
     * @param int $seqNum PDU sequence number to check
     *
     * @return bool true if ACK has been recieved, false otherwise
     */
    function ackRecvd($seqNum)
    {
        if (array_key_exists($seqNum, $this->_pduStack)) {
            return false;
        }
        return true;
    }
    
    /**
     * Checks to see if the sending stack is full
     * 
     * @return bool true if full, false otherwise
     */
    function sendStackFull()
    {
        if (count($this->_pduStack) > MAX_STACK_SIZE) {
            return true;
        }
        return false;
    }
}

?>
