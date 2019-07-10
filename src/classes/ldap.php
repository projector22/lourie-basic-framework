<?php
/**
 * Various methods for performing and executing LDAP queries
 * 
 * @author  Gareth Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

class LDAP {

    /**
     * Variables representing various LDAP components
     * 
     * @since   0.1 Pre-alpha
     */

    private $dn;
    private $password;
    private $address;
    private $port;
    private $search_ou;

    private $results;
    
    /**
     * Consructor method, things to do when the class is loaded.
     * If data is not specified, it will pull from the database
     * 
     * @param   string  $dn         The specified domain name               Default: null
     * @param   string  $password   The specified domain password           Default: null
     * @param   string  $address    The specified domain address            Default: null
     * @param   string  $port       The specified domain port               Default: null
     * @param   string  $search_ou  A custom search ou in which to look     Default: null
     * 
     * @since   0.1 Pre-alpha
     */

    public function __construct( $dn=null, $password=null, $address=null, $port=null, $search_ou=null ){
        $db_control = new DatabaseControl;
        $this->results = $db_control->sql_select( " SELECT * FROM " . LDAP_CONFIG )[0];

        if ( !is_null( $dn ) ){
            $this->dn = $dn;
        } else {
            $this->dn = $this->results['dn'];
        }
        if ( !is_null( $password ) ){
            $this->password = $password;
        } else {
            $this->password = $this->results['dn_password'];
        }
        if ( !is_null( $address) ){
            $this->address = $address;
        } else {
            $this->address = $this->results['address'];
        }
        if ( !is_null( $port ) ){
            $this->port = $port;
        } else {
            $this->port = $this->results['port'];
        }
        if ( !is_null( $search_ou ) ){
            $this->search_ou = $search_ou;
        } else {
            $this->search_ou = $this->results['search_ou'];
        }

    }//__construct

    /**
     * Test an ldap sync with the assigned data
     * 
     * @return  boolean true    If the sync works
     * @return  boolean false   If the sync fails
     * 
     * @since   0.1 Pre-alpha
     */

    public function ldap_login(){
        $ldap_con = ldap_connect( $this->address, $this->port );
        ldap_set_option( $ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3 );
        if ( ldap_bind( $ldap_con, $this->dn, $this->password ) ){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Pull entries out of an Active Directory via LDAP into an array
     * 
     * @param   string  $object_category    The type of search being performed                      Default: user
     * @param   string  $sam_account_name   A specific filter for SAMAccountNames, * returns all    Default: *
     * 
     * @return  array   All the search results searchable in an array
     * @return  boolean false   If the search fails
     * 
     * @since   0.1 Pre-alpha
     */

    public function ldap_search( $object_category='user', $sam_account_name='*', $search_ou=null ){
        $ldap_con = ldap_connect( $this->address, $this->port );
        ldap_set_option( $ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3 );
        if ( ldap_bind( $ldap_con, $this->dn, $this->password ) ){
            //Would like to make this more dynamic
            $filter = "(&(objectCategory=$object_category)(samaccountname=$sam_account_name))";
            if ( !is_null( $search_ou ) ){
                $results = ldap_search( $ldap_con, $search_ou, $filter );
            } else {
                $results = ldap_search( $ldap_con, $this->search_ou, $filter );
            }//if check for custom search ou
            return ldap_get_entries( $ldap_con, $results );
        } else {
            return false;
        }//if bind successful
    }
    /*
    Example of using data from ldap_search()

    <?php

    $ldap_connect = new LDAP;
    $entries = $ldap_connect->ldap_search();
    foreach ( $entries as $i => $entry ){
        if ( $entries[$i]['objectclass'][1] == 'person' ){
            $heading = "(school_num, last_name, first_name, year_created)";
            $data = "('" . $entries[$i]['samaccountname'][0] . "', '" . $entries[$i]['sn'][0] . "', '" . $entries[$i]['givenname'][0] . "', '$year')";
            $sql = "INSERT INTO $persons $heading VALUES $data";
            if ( mysqli_query( $link, $sql ) ){
                $a++;
            } else {
                $k++;
            }
        }//if a person
    }
    */

    /**
     * Checks if LDAP is enabled on the system or not
     * 
     * @return  boolean     Whether LDAP is enabled or not
     * 
     * @since   0.1 Pre-alpha
     */

    public function ldap_status_enabled(){
        if ( $this->results['ldap_enabled'] == 1 ){
            return true;
        } else {
            return false;
        }
    }
}
