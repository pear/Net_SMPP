<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Vendor support class
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
 * @since      0.3.4dev1
 * @link       http://pear.php.net/package/Net_SMPP
 */

// Place includes, constant defines and $_GLOBAL settings here.

/**
 * Vendor support class
 *
 * @category   Networking
 * @package    Net_SMPP
 * @author     Ian Eure <ieure@php.net>
 * @copyright  (c) Copyright 2005 WebSprockets, LLC.
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @version    CVS:     $Revision$
 * @since      0.3.4dev1
 * @link       http://pear.php.net/package/Net_SMPP
 */
class Net_SMPP_Vendor {
    /**
     * Vendor parameters
     *
     * This variable is in the same format as {@link Net_SMPP_Command::$_defs},
     * but only contains the vendor PDU-specufic parameters.
     *
     * @var     array
     * @access  protected
     * @see     Net_SMPP_Command::$_defs
     */
     var $_vdefs = array();


    /**
     * Vendor commands
     *
     * @return  array
     * @access  protected
     * @see     Net_SMPP_Command::$_defs
     * @static
     */
    function &_commandList()
    {
        static $commands = array();

        return $commands;
    }

    /**
     * Vendor optional parameters
     *
     * @var     array
     * @access  protected
     * @static
     */
    function &_optionalParams()
    {
        static $params = array();

        return $params;
    }

    /**
     * Vendor status descriptions
     *
     * @return  array
     * @access  protected
     * @static
     */
    function &_statusDescs()
    {
        static $descs = array();

        return $descs;
    }

    /**
     * Load a vendor
     *
     * @param       string  $vendor  Vendor to load
     * @deprecated  Deprecated in Net_SMPP 0.4.0
     */
    function __load($vendor)
    {
        require_once 'Net/SMPP/Command.php';

        $cmds =& Net_SMPP_Command::_commandList();
        $params =& Net_SMPP_Command::_optionalParams();
        $descs =& Net_SMPP_PDU::_statusDescs();

        $file = 'Net/SMPP/Vendor/' . $vendor . '.php';
        include_once $file;
        $class = 'Net_SMPP_Vendor_' . $vendor;
        if (!class_exists($class)) {
            return false;
        }

        $vcmds =& call_user_func(array($class, '_commandList'));
        $vparams =& call_user_func(array($class, '_optionalParams'));
        $vdescs =& call_user_func(array($class, '_statusDescs'));

        if (count($vcmds)) {
            $cmds = array_merge($cmds, $vcmds);
        }

        if (count($vparams)) {
            $params = array_merge($params, $vparams);
        }

        if (count($vdescs)) {
            // Can't use array_merge here, since it munges numeric keys
            foreach ($vdescs as $code => $desc) {
                $descs[$code] = $desc;
            }
        }
    }

    /**
     * Get a static instance of a vendor
     *
     * @param    string  $vendor  Vendor to get an instance of
     * @return   object  Vendor instance
     * @since    Net_SMPP 0.4.0
     * @static
     */
    function &singleton($vendor)
    {
        static $instances = array();

        if (!isset($instances[$vendor])) {
            $instances[$vendor] =& Net_SMPP_Vendor::factory($vendor);
        }

        return $instances[$vendor];
    }

    /**
     * Get an instance of a vendor class
     *
     * @param    string  $vendor  Vendor to get an instance of
     * @return   object  Vendor instance
     * @since    Net_SMPP 0.4.0
     * @static
     */
    function factory($vendor)
    {
        $file = 'Net/SMPP/Vendor/' . $vendor . '.php';
        include_once $file;
        $class = 'Net_SMPP_Vendor_' . $vendor;
        if (!class_exists($class)) {
            return false;
        }

        return new $class;
    }


    /**
     * Determine if there is a vendor PDU for a command
     *
     * @param   string   $vendor   Vendor to check
     * @param   string   $command  Command to check
     * @return  boolean  true if there's a vendor command, false otherwise
     */
    function PDUexists($vendor, $command)
    {
        $file = 'Net/SMPP/Vendor/' . $vendor . '/'. $command .'.php';
       
        $paths = explode(':', get_include_path());
        foreach ($paths as $path) {
            if (substr($path, -1) == '/') {
                $fullpath = $path.$file;
            } else {
                $fullpath = $path.'/'.$file;
            }
            if (file_exists($fullpath)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get an instance of a vendor PDU class
     *
     * @param   string  $vendor   Vendor
     * @param   string  $command  Command class
     * @return  mixed   Net_SMPP_PDU::factory()'s return value
     * @see     Net_SMPP_PDU::factory()
     * @static
     */
    function PDU($vendor, $command, $args = array())
    {
        $file = dirname(__FILE__) . '/Vendor/' . $vendor . '/' . $command . '.php';
        $class = 'Net_SMPP_Command_Vendor_' . $vendor . '_' . $command;

        // Make sure the vendor has been loaded.
        Net_SMPP_Vendor::singleton($vendor);

        include_once $file;
        if (!class_exists($class)) {
            return false;
        }
        return new $class($command, $args);
    }

    /**
     * Parse a binary PDU
     *
     * @param   string  $vendor  Vendor
     * @param   string  $data    Raw (binary) PDU data
     * @return  object  Net_SMPP_Command_* instance
     */
    function &parsePDU($vendor, $data)
    {
        require_once 'Net/SMPP/PDU.php';
        $command = Net_SMPP_PDU::extractCommand($data);
        if ($command === false) {
            return $false;
        }

        if (Net_SMPP_Vendor::PDUexists($vendor, $command)) {
            $pdu =& Net_SMPP_Vendor::PDU($vendor, $command, array('sequence' => 'dummy'));
        } else {
            $pdu =& Net_SMPP::PDU($command, array('sequence' => 'dummy'));
        }
        $pdu->parse($data);
        return $pdu;
    }
}

?>
