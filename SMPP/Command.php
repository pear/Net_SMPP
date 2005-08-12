<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Net_SMPP Command class and data
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
 * @copyright  2005 WebSprockets, LLC.
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @version    CVS:     $Revision$
 * @since      Release 0.0.1dev2
 * @link       http://pear.php.net/package/Net_SMPP
 */

// Place includes, constant defines and $_GLOBAL settings here.
require_once 'Net/SMPP/PDU.php';

// These are the keyvalues for these optional paramaters

/**
 * SMPP v3.4 TON (Type-of-Number) values
 *
 * @see  Net_SMPP_Command_submit_sm::$source_addr_ton
 */
define('NET_SMPP_TON_UNK',     0x00); // Unknown
define('NET_SMPP_TON_INTL',    0x01); // International
define('NET_SMPP_TON_NATNL',   0x02); // National
define('NET_SMPP_TON_NWSPEC',  0x03); // Network-specific
define('NET_SMPP_TON_SBSCR',   0x04); // Subscriber number
define('NET_SMPP_TON_ALNUM',   0x05); // Alphanumberic
define('NET_SMPP_TON_ABBREV',  0x06); // Abbreviated

/**
 * SMPP v3.4 NPI (Numbering Plan Indicator) values
 *
 * @see  Net_SMPP_Command_submit_sm::$source_addr_npi
 */
define('NET_SMPP_NPI_UNK',    0x00); // Unknown
define('NET_SMPP_NPI_ISDN',   0x01); // ISDN (E163/E164)
define('NET_SMPP_NPI_DATA',   0x03); // Data (X.121)
define('NET_SMPP_NPI_TELEX',  0x04); // Telex (F.69)
define('NET_SMPP_NPI_LNDMBL', 0x06); // Land Mobile (E.212)
define('NET_SMPP_NPI_NATNL',  0x08); // National
define('NET_SMPP_NPI_PRVT',   0x09); // Private
define('NET_SMPP_NPI_ERMES',  0x0A); // ERMES
define('NET_SMPP_NPI_IP',     0x0E); // IPv4
define('NET_SMPP_NPI_WAP',    0x12); // WAP

/**
 * SMPP v3.4 encoding types
 *
 * @see  Net_SMPP_Command_submit_sm::$data_coding
 */
define('NET_SMPP_ENCODING_DEFAULT',   0x00); // SMSC Default Alphabet
define('NET_SMPP_ENCODING_IA5',       0x01); // IA5 (CCITT T.50)/ASCII (ANSI X3.4)
define('NET_SMPP_ENCODING_BINARY',    0x02); // Octet unspecified (8-bit binary)
define('NET_SMPP_ENCODING_ISO88591',  0x03); // Latin 1 (ISO-8859-1)
define('NET_SMPP_ENCODING_BINARY2',   0x04); // Octet unspecified (8-bit binary)
define('NET_SMPP_ENCODING_JIS',       0x05); // JIS (X 0208-1990)
define('NET_SMPP_ENCODING_ISO88595',  0x06); // Cyrllic (ISO-8859-5)
define('NET_SMPP_ENCODING_ISO88598',  0x07); // Latin/Hebrew (ISO-8859-8)
define('NET_SMPP_ENCODING_ISO10646',  0x08); // UCS2 (ISO/IEC-10646)
define('NET_SMPP_ENCODING_PICTOGRAM', 0x09); // Pictogram Encoding
define('NET_SMPP_ENCODING_ISO2022JP', 0x0A); // ISO-2022-JP (Music Codes)
define('NET_SMPP_ENCODING_EXTJIS',    0x0D); // Extended Kanji JIS(X 0212-1990)
define('NET_SMPP_ENCODING_KSC5601',   0x0E); // KS C 5601

/**
 * SMPP v3.4 langauge types
 *
 * @see  Net_SMPP_Command_submit_sm::$language_indicator
 */
define('NET_SMPP_LANG_DEFAULT', 0x00);
define('NET_SMPP_LANG_EN',      0x01);
define('NET_SMPP_LANG_FR',      0x02);
define('NET_SMPP_LANG_ES',      0x03);
define('NET_SMPP_LANG_DE',      0x04);


/**
 * Base Net_SMPP PDU command class
 *
 * This class is the base from which the command-specific classes inherit.
 * It contains functions common to the parsing and generation of the paramaters
 * for all SMPP commands.
 *
 * @category   Networking
 * @package    Net_SMPP
 * @author     Ian Eure <ieure@php.net>
 * @copyright  2005 WebSprockets, LLC.
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @version    CVS:     $Revision$
 * @since      Release 0.0.1dev2
 * @link       http://pear.php.net/package/Net_SMPP
 */
class Net_SMPP_Command extends Net_SMPP_PDU
{
    /**
     * Parameter defs for this command
     *
     * *ORDER IS SIGNIFICANT!* Required paramaters *MUST* be listed in the
     * order as defined by the protocol. Optional paramaters *MUST* come
     * after all the required paramaters.
     *
     * This should look like:
     *
     * $_defs = array(
     *     'field_name' => array(
     *         'type' => 'field_type',
     *         'size' => field_size,
     *         'max'  => field_max_size
     *     )
     *  );
     *
     * 'type' is one of: int, string, ostring.
     * - int: your basic integer. 'size' is the number of bytes the value
     *        will be packed into. 'max' is ignored.
     * - string: a basic string. 'size' is the size of the string in bytes
     *           if the length of the string is less than 'size,' it will be
     *           null-padded. 'max' is the maximum length for variable-length
     *           strings. Only set one of 'max' or 'size.'
     * - ostring: Non-null-terminated, variable length octet string.
     *
     * @var     array
     * @access  protected
     */
    var $_defs = array();


    /**
     * 4.x constructor
     *
     * @param   array  $args  Values to set
     * @return  void
     * @see     __construct()
     */
    function Net_SMPP_Command($command, $args = array())
    {
        $this->__construct($command, $args);
    }

    /**
     * 5.x/main constructor
     *
     * @param   array  $args  Values to set
     * @return  void
     * @see     set()
     */
    function __construct($command, $args = array())
    {
        $this->command = $command;
        if (!isset($args['sequence'])) {
            $this->sequence = Net_SMPP::nextSeq();
        }
        $this->status = NET_SMPP_ESME_ROK;

        // Merge vendor field definitions
        if ($this->isVendor() && !empty($this->_vdefs)) {
            $this->_defs = array_merge($this->_defs, $this->_vdefs);
        }

        $this->set($args);
    }

    /**
     * Get the name of a command from it's ID
     *
     * @param   int    $cmdcode  Command ID
     * @return  mixed  String command name, or false
     */
    function commandName($cmdcode)
    {
        $cmds =& Net_SMPP_Command::_commandList();

        if (in_array($cmdcode, $cmds)) {
            return array_search($cmdcode, $cmds);
        }
        return false;
    }

    /**
     * Get the ID of a command from it's name
     *
     * @param   int    $cmdname  Command name
     * @return  mixed  Int command ID, or false
     */
    function commandCode($cmdname)
    {
        $cmds =& Net_SMPP_Command::_commandList();

        if (isset($cmds[$cmdname])) {
            return $cmds[$cmdname];
        }
        return false;
    }

    /**
     * Generate the binary data from the object
     *
     * This is the workhorse of this class (and all the other
     * Net_SMPP_Command_* classes). It's responsible for generating the binary
     * protocol data from the fields in the object, and is the opposite of
     * parse().
     *
     * @return  string
     * @see     _packFormat()
     */
    function generateParams()
    {
        // Is there a prep() method?
        if (method_exists($this, 'prep')) {
            $this->prep();
        }

        $body = '';
        foreach ($this->_defs as $field => $def) {
            if ($this->fieldIsOptional($field)) {
                if (!isset($this->$field)) {
                    continue;
                }
                $body .= $this->_generateOptHeader($field);
            }

            switch ($def['type']) {
                case 'int':
                    $body .= $this->_generateInt($field);
                    break;

                case 'string':
                    $body .= $this->_generateString($field);
                    break;

                case 'ostring':
                    $body .= $this->_generateOString($field);
                    break;
            }
        }
        return $body;
    }

    /**
     * Generate a header for an optional param
     *
     * @param   string  $field  Paramater to generate header for
     * @return  string  Binary header representation
     * @access  private
     */
    function _generateOptHeader($field)
    {
        static $oparams;

        // These are always the same, so we can drop them into a static to save
        // function call overhead.
        if (!isset($oparams)) {
            $oparams =& Net_SMPP_Command::_optionalParams();
        }

        // Get the vendor-specific optional params
        if ($this->isVendor()) {
            $v =& Net_SMPP_Vendor::singleton($this->vendor);
            $voparams =& $v->_optionalParams();
        }

        // We have to check the vendor optional parameters first...
        // This allows vendor optional params to override the standard ones.
        if (isset($voparams[$field])) {
            $fid = $voparams[$field];
        } else {
            $fid = $oparams[$field];
        }

        if (isset($this->_defs[$field]['size'])) {
            $len = $this->_defs[$field]['size'];
        } else if ($this->_defs[$field]['type'] == 'ostring') {
            $len = strlen($this->$field);
        } else {
            $len = strlen($this->$field + 1);
        }

        // Generate the binary data - field ID first, then field len
        return pack('n', $fid) .
               pack('n', $len);
    }

    /**
     * Generate an integer value
     *
     * @param   string  $field  Paramater to generate
     * @return  string  Binary representation
     * @access  private
     */
    function _generateInt($field)
    {
        $pf = $this->_packFormat($field);
        return @pack($pf, $this->$field);
    }

    /**
     * Generate a string value
     *
     * @param   string  $field  Paramater to generate
     * @return  string  Binary string representation
     * @access  private
     */
    function _generateString($field)
    {
        if (isset($this->_defs[$field]['size'])) {
            // Fixed-length - NULL pad
            $val = str_pad($this->$field, $this->_defs[$field]['size'], chr(0));
        } else if (isset($this->_defs[$field]['max'])) {
            if (strlen($this->$field) > $this->_defs[$field]['max']) {
                // FIXME - add warning.
                $this->field = substr($this->$field, 0, $this->_defs[$field]['max'] - 1);
            }

            $val = $this->$field . chr(0);
        }
        return $val;
    }

    /**
     * Generate an octet string value
     *
     * Octet strings do not have a null terminator
     *
     * @param   string  $field  Paramater to generate
     * @return  string  Binary string representation
     * @access  private
     */
    function _generateOString($field)
    {
        return $this->$field;
    }

    /**
     * Get the format argument for pack()
     *
     * @param   $field   Field to get pack argument for
     * @return  string   Pack format for $field
     * @access  private
     */
    function _packFormat($field)
    {
        switch ($this->_defs[$field]['size']) {
            case 1:
                return 'C';
                break;
            case 2:
                return 'n';
                break;
            case 3:
                return 'N';
                break;
        }
        return null;
    }

    /**
     * Parse a fixed-length chunk from a PDU
     *
     * @param   string  $field  Field to put this data in
     * @param   string  $data   Raw PDU data
     * @param   int     $pos    Position data starts in the PDU
     * @return  void
     * @access  private
     */
    function _parseInt($field, &$data, &$pos)
    {
        $len = $this->_defs[$field]['size'];
        $this->$field = implode(null, $this->_unpack($this->_packFormat($field),
            substr($data, $pos, $len)));
            $pos += $len;
    }

    /**
     * Parse a variable-length string from a PDU
     *
     * @param   string  $field  Field to put this data in
     * @param   string  $data   Raw PDU data
     * @param   int     $pos    Position data starts in the PDU
     * @return  void
     * @todo    Handle GSM char encoding
     * @note    The fixed-length code is probably wrong.
     * @access  private
     */
    function _parseString($field, &$data, &$pos)
    {
        $fe = strpos($data, null, $pos);  // End of the string
        $fl = $fe - $pos;                 // String length

        $this->$field = substr($data, $pos, $fl);
        $pos += $fl + 1;                  // Add one for the NULL terminator
    }

    /**
     * Parse an  octet string from a PDU
     *
     * @param   string  $field  Field to put this data in
     * @param   string  $data   Raw PDU data
     * @param   int     $pos    Position data starts in the PDU
     * @param   int     $len    String length or NULL
     * @return  void
     * @todo    Handle GSM char encoding
     * @access  private
     */
    function _parseOString($field, &$data, &$pos, $len = null)
    {
        if (is_null($len)) {
            $lenf = $this->_defs[$field]['lenField'];
            $len = $this->$lenf;
        }

        $this->$field = substr($data, $pos, $len);
        $pos += $len;
    }

    /**
     * Is this a fixed-length field?
     *
     * @param   string   $field  Field to test
     * @return  boolean  true if it is, false otherwise
     * @access  protected
     */
    function isFixed($field)
    {
        if (isset($this->_def[$field]['size'])) {
            return true;
        }
        return false;
    }

    /**
     * Parse data into the object structure
     *
     * @param   string   $data  Data to parse
     * @return  boolean  true on success, false otherwise
     */
    function parseParams($data)
    {
        $pos = 0;
        $dl = strlen($data);
        foreach ($this->_defs as $field => $def) {
            // Abort the loop if we're at the end of the data, or if we
            // encounter an optional paramater
            if ($pos == $dl ||
                $this->fieldIsOptional($field)) {
                break;
            }

            switch ($def['type']) {
                case 'int':
                    $this->_parseInt($field, $data, $pos);
                    break;

                case 'string':
                    $this->_parseString($field, $data, $pos);
                    break;

                case 'ostring':
                    $this->_parseOString($field, $data, $pos);
                    break;
            }
        }

        // Are there optional paramaters left?
        if ($pos < $dl) {
            $this->parseOptionalParams(substr($data, $pos));
        }
    }

    /**
     * Parse optional paramaters
     *
     * @param   string  $data  Optional paramaters to parse
     * @return  void
     * @access  protected
     */
    function parseOptionalParams($data)
    {
        $oparams =& Net_SMPP_Command::_optionalParams();

        /**
         * Optional params have the `TLV' format:
         *
         * - Type   (2 bytes)
         * - Length (2 bytes)
         * - Value  (variable, `Length' bytes)
         */

        $dl = strlen($data);
        $pos = 0;
        while ($pos < $dl) {
            $type = implode(null, unpack('n', substr($data, $pos, 2)));
            $field = array_search($type, $oparams);
            $pos += 2;
            if ($field == null || $field == false) {
                // FIXME - error/warning here
                return;
            }

            $len = implode(null, unpack('n', substr($data, $pos, 2)));
            $pos += 2;
            switch ($this->_defs[$field]['type']) {
                case 'int':
                    $this->_parseInt($field, $data, $pos);
                    break;

                case 'string';
                    $this->_parseString($field, $data, $pos);
                    break;

                case 'ostring':
                    $this->_parseOString($field, $data, $pos, $len);
                    break;
            }
        }
    }

    /**
     * Does this field exist?
     *
     * @param   string   $field  Field to check
     * @return  boolean  true if it exists, false otherwise
     * @access  protected
     */
    function fieldExists($field)
    {
        return isset($this->_defs[$field]);
    }

    /**
     * Is this field optional?
     *
     * @param   string   $field  Field name
     * @return  boolean  true if optional, false otherwise
     */
    function fieldIsOptional($field)
    {
        $oparams =& Net_SMPP_Command::_optionalParams();

        if (array_key_exists($field, $oparams)) {
            return true;
        } else if ($this->isVendor()) {
            $v =& Net_SMPP_Vendor::singleton($this->vendor);
            $vparams =& $v->_optionalParams();
            return array_key_exists($field, $vparams);
        }
        return false;
    }

    /**
     * Set values in this object
     *
     * Unknown values are ignored.
     *
     * @param   array  $args  Values to set
     * @return  void
     */
    function set($args = array())
    {
        foreach ($args as $k => $v) {
            if ($this->fieldExists($k)) {
                $this->$k = $v;
            }
        }
    }

    /**
     * Get the list of SMPP v3.4 commands
     *
     * @return  array
     * @access  protected
     * @static
     */
    function &_commandList()
    {
        static $commands = array(
            'generic_nack'          => 0x80000000,
            'bind_receiver'         => 0x00000001,
            'bind_receiver_resp'    => 0x80000001,
            'bind_transmitter'      => 0x00000002,
            'bind_transmitter_resp' => 0x80000002,
            'query_sm'              => 0x00000003,
            'query_sm_resp'         => 0x80000003,
            'submit_sm'             => 0x00000004,
            'submit_sm_resp'        => 0x80000004,
            'deliver_sm'            => 0x00000005,
            'deliver_sm_resp'       => 0x80000005,
            'unbind'                => 0x00000006,
            'unbind_resp'           => 0x80000006,
            'replace_sm'            => 0x00000007,
            'replace_sm_resp'       => 0x80000007,
            'cancel_sm'             => 0x00000008,
            'cancel_sm_resp'        => 0x80000008,
            'bind_transceiver'      => 0x00000009,
            'bind_transceiver_resp' => 0x80000009,
            'outbind'               => 0x0000000B,
            'enquire_link'          => 0x00000015,
            'enquire_link_resp'     => 0x80000015,
            'submit_multi'          => 0x00000021,
            'submit_multi_resp'     => 0x80000021,
            'alert_notification'    => 0x00000102,
            'data_sm'               => 0x00000103,
            'data_sm_resp'          => 0x80000103
        );

        return $commands;
    }

    /**
     * Get list of optional paramaters
     *
     * @return  array
     * @access  protected
     * @static
     */
     function &_optionalParams()
     {
        static $params = array(
            'dest_addr_subunit'           => 0x0005,
            'dest_network_type'           => 0x0006,
            'dest_bearer_type'            => 0x0007,
            'dest_telematics_id'          => 0x0008,
            'source_addr_subunit'         => 0x000D,
            'source_network_type'         => 0x000E,
            'source_bearer_type'          => 0x000F,
            'source_telematics_id'        => 0x0010,
            'qos_time_to_live'            => 0x0017,
            'payload_type'                => 0x0019,
            'additional_status_info_text' => 0x001D,
            'receipted_message_id'        => 0x001E,
            'ms_msg_wait_facilities'      => 0x0030,
            'privacy_indicator'           => 0x0201,
            'source_subaddress'           => 0x0202,
            'dest_subaddress'             => 0x0203,
            'user_message_reference'      => 0x0204,
            'user_response_code'          => 0x0205,
            'source_port'                 => 0x020A,
            'destination_port'            => 0x020B,
            'sar_msg_ref_num'             => 0x020C,
            'language_indicator'          => 0x020D,
            'sar_total_segments'          => 0x020E,
            'sar_segment_seqnum'          => 0x020F,
            'SC_interface_version'        => 0x0210,
            'callback_num_pres_ind'       => 0x0302,
            'callback_num_atag'           => 0x0303,
            'number_of_messages'          => 0x0304,
            'callback_num'                => 0x0381,
            'dpf_result'                  => 0x0420,
            'set_dpf'                     => 0x0421,
            'ms_availability_status'      => 0x0422,
            'network_error_code'          => 0x0423,
            'message_payload'             => 0x0424,
            'delivery_failure_reason'     => 0x0425,
            'more_messages_to_send'       => 0x0426,
            'message_state'               => 0x0427,
            'ussd_service_op'             => 0x0501,
            'display_time'                => 0x1201,
            'sms_signal'                  => 0x1203,
            'ms_validity'                 => 0x1204,
            'alert_on_message_delivery'   => 0x130C,
            'its_reply_type'              => 0x1380,
            'its_session_info'            => 0x1383
        );

        return $params;
     }
}