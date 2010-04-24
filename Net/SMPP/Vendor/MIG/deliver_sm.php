<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MIG deliver_sm command class
 *
 * PHP versions 4 and 5
 *
 */

// Place includes, constant defines and $_GLOBAL settings here.
require_once 'Net/SMPP/Command/deliver_sm.php';


class Net_SMPP_Command_Vendor_MIG_deliver_sm extends Net_SMPP_Command_deliver_sm {

    /**
     * The vendor this class belongs to
     *
     * @var  string
     */
    var $vendor = 'MIG';



    // Vendor-specific TLVs



    var $MIG_operator;
    var $MIG_guid;
    var $MIG_tariff;
    var $MIG_tariff_class;
    var $MIG_operator_country;
    var $MIG_operator_network;
    var $MIG_User_ID;
    var $MIG_Subscription;
    var $MIG_Billing_Text;



    /**
     * Vendor definitions
     *
     * @var     array
     * @access  protected
     */
    var $_vdefs = array(
        'MIG_operator' => array(
            'type' => 'ostring',
            'max' => 10
        ),
        'MIG_guid' => array(
            'type' => 'ostring',
            'size' => 50
        )
    );
}
