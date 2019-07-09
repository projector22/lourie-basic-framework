<?php
/**
 * 
 * Various methods for performing and executing LDAP queries
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

class LDAP {

    //Still TO DO - Create a method of searching through an ldap query and returning the data

    /**
     * 
     * Variables representing various LDAP components
     * 
     * @since   0.1 Pre-alpha
     */

    private $dn;
    private $password;
    private $address;
    private $port;

    
    /**
     * Consructor method, things to do when the class is loaded.
     * If data is not specified, it will pull from the database
     * 
     * @param   string  $dn         The specified domain name       Default: null
     * @param   string  $password   The specified domain password   Default: null
     * @param   string  $address    The specified domain address    Default: null
     * @param   string  $port       The specified domain port       Default: null
     * 
     * @since   0.1 Pre-alpha
     */

    public function __construct( $dn=null, $password=null, $address=null, $port=null ){
        $db_control = new DatabaseControl;
        $results = $db_control->sql_select( " SELECT * FROM " . LDAP_CONFIG );

        if ( !is_null( $dn ) ){
            $this->dn = $dn;
        } else {
            $this->dn = $results[0]['dn'];
        }
        if ( !is_null( $password ) ){
            $this->password = $password;
        } else {
            $this->password = $results[0]['dn_password'];
        }
        if ( !is_null( $address) ){
            $this->address = $address;
        } else {
            $this->address = $results[0]['address'];
        }
        if ( !is_null( $port ) ){
            $this->port = $port;
        } else {
            $this->port = $results[0]['port'];
        }

    }//__construct

    /**
     * 
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

}
/*
$ldap_con = ldap_connect( $address, $port );
ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
$conn = ldap_bind( $ldap_con, $dn, $dn_password );
if ( $conn ){
    $filter = "(&(objectCategory=user)(samaccountname=*))";
    $results = ldap_search( $ldap_con, $search_ou, $filter );
    $entries = ldap_get_entries( $ldap_con, $results ) or die( 'Sync failed ' );
} else { 
    action_error( "sync failed" );
    die; 
}

$a = 0;//count num of new entries
$k = 0;//count num of failures

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