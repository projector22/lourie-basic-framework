<?php

namespace LBS\Tools\LDAP;

use \Exception;
use App\Db\Data\GeneralConfigData;

/**
 * Various methods for performing and executing LDAP queries
 * 
 * use LBS\Tools\LDAP\LDAPHandler;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.1.0
 * @since   3.11.0  Moved to LBS\Tools\LDAPHandler and class renamed DownloadHandler from LDAP
 */

class LDAPHandler {

    /**
     * The ldap connection variable
     * 
     * @var object  $ldap_con
     * 
     * Technically a 'Resource'
     * 
     * @access  private
     * @since   3.11.0
     */

    private $ldap_con;

    /**
     * The domain binding name of the LDAP server
     * 
     * @var string  $dn
     * 
     * @access  private
     * @since   3.1.0
     */

    private string $dn;

    /**
     * The domain binding password of the LDAP server
     * 
     * @var string  $dn_password
     * 
     * @access  private
     * @since   3.1.0
     */

    private string $dn_password;

    /**
     * The ip or domain address of the LDAP server
     * 
     * @var string  $ldap_server
     * 
     * @access  private
     * @since   3.1.0
     */

    private string $ldap_server;

    /**
     * The port being used by the LDAP server
     * 
     * @var string  $port
     * 
     * @access  private
     * @since   3.1.0
     */

    private string $port;

    /**
     * Constructor method, things to do when the class is loaded.
     * If data is not specified, it will pull from the database
     * 
     * @param   string  $dn             The specified domain name               Default: null
     * @param   string  $dn_password    The specified domain dn_password        Default: null
     * @param   string  $ldap_server    The specified domain ldap_server        Default: null
     * @param   string  $port           The specified domain port               Default: null
     * 
     * @access  public
     * @since   3.1.0
     * @since   3.11.0  Removed param $search_ou
     */

    public function __construct( 
        ?string $dn = null,
        ?string $dn_password = null,
        ?string $ldap_server = null,
        ?string $port = null
    ) {
        // Check if PHP_LDAP exists on the system
        $this->check_ldap();

        $this->ldap_config = new GeneralConfigData;
        $this->ldap_config->get_ldap_config();

        $fields = [ 
            'ldap_enabled', 'ldap_server', 'dn', 'dn_password', 'port', 'sync_teachers_by_ou', 'sync_teachers_by_group', 'sync_teachers_ou', 
            'sync_teachers_group', 'delete_accounts_not_present_on_server', 'sync_students_by_ou', 'sync_students_by_group', 'sync_students_ou', 
            'sync_students_group', 
        ];

        foreach ( $fields as $field ) {
            $this->$field = $this->ldap_config->$field;
        }

        if ( !is_null( $dn ) ) {
            $this->dn = $dn;
        }
        if ( !is_null( $dn_password ) ) {
            $this->dn_password = $dn_password;
        }
        if ( !is_null( $ldap_server) ) {
            $this->ldap_server = $ldap_server;
        }
        if ( !is_null( $port ) ) {
            $this->port = $port;
        }

        $this->ldap_con = ldap_connect( $this->ldap_server, $this->port );
        if ( !$this->ldap_con ) {
            echo "LDAP connection failed";
            die;
        }
        ldap_set_option( $this->ldap_con, LDAP_OPT_REFERRALS, 0 );
        ldap_set_option( $this->ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3 );
    } //__construct


    /**
     * Test an ldap sync with the assigned data
     * 
     * @return  boolean true    If the sync works
     * @return  boolean false   If the sync fails
     * 
     * @access  public
     * @since   3.1.0
     */

    public function ldap_login(): bool {
        if ( @ldap_bind( $this->ldap_con, $this->dn, $this->dn_password ) ) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Pull entries out of an Active Directory via LDAP into an array
     * 
     * @param   string      $object_category    The type of search being performed                      Default: user
     * @param   string      $sam_account_name   A specific filter for SAMAccountNames, * returns all    Default: *
     * @param   string|bool $search_ou          Search context in the LDAP search                       Default: null
     * 
     * @return  array   All the search results searchable in an array
     * @return  boolean false   If the search fails
     * 
     * @access  public
     * @since   3.1.0
     */

    public function ldap_search( string $object_category = 'user', string $sam_account_name = '*', ?string $search_ou = null ): array|bool {
        if ( @ldap_bind( $this->ldap_con, $this->dn, $this->dn_password ) ) {
            // Would like to make this more dynamic
            $filter = "(&(objectCategory=$object_category)(samaccountname=$sam_account_name))";
            if ( !is_null( $search_ou ) ) {
                $results = ldap_search( $this->ldap_con, $search_ou, $filter );
            } else {
                $results = ldap_search( $this->ldap_con, $this->search_ou, $filter );
            }// if check for custom search ou
            return ldap_get_entries( $this->ldap_con, $results );
        } else {
            return false;
        }
    }


    /**
     * Check that LDAP is enabled and exists
     * 
     * @access  public
     * @since   3.6.1
     */

    private function check_ldap(): void {
        if ( !function_exists( 'ldap_connect' ) ) {
            echo "<pre>";
            throw new Exception( "LDAP not enabled" );
            echo "</pre>";
        }
    }


    /**
     * Whether dealing with students or teachers students.
     *                          
     * Options: 'teachers', 'students', 'other'
     * 
     * @var string  $context    Default: 'teachers'
     * 
     * @access  public
     * @since   3.11.0
     */

    public string $context = 'teachers';

    /**
     * Get all the members of a group
     * 
     * @param   string  $group_dn   Set a custom or alternative group_dn to the teacher or student context
     * 
     * @return array|bool  False if failed
     * 
     * @access  public
     * @since   3.11.0
     * @since   3.12.1  Added recursiveness to get members of groups within the defined group
     */

    public function get_group_members( ?string $group_dn = null ): array|bool {
        $get_group_location = function ( $context ) {
            // Search through the whole of the Domain Controller
            $hold = array_reverse( explode( ',', $context ) );
            foreach ( $hold as $i => $item ) {
                if ( substr ( $item, 0, 2 ) !== 'DC' ) {
                    unset( $hold[$i] );
                }
            }
            return implode( ',', array_reverse ( $hold ) );
        };
        if ( @ldap_bind( $this->ldap_con, $this->dn, $this->dn_password ) ) {
            switch ( $this->context ) {
                case 'teachers':
                    $context = $this->sync_teachers_group;
                    $group_location = $get_group_location( $context );
                    break;
                case 'students':
                    $context = $this->sync_students_group;
                    $group_location = $get_group_location( $context );
                    break;
                default:
                    if ( !is_null( $group_dn ) ) {
                        $context = $group_dn;
                        $group_location = $get_group_location( $context );
                    } else {
                        echo "Invalid context selected";
                        return false;
                    }
            }
            $filter = "(memberof={$context})";
            $results = ldap_search( $this->ldap_con, $group_location, $filter );
            $entries = ldap_get_entries( $this->ldap_con, $results );
            $context_hold = $this->context;
            $new_members = [];
            foreach ( $entries as $index => $entry ) {
                if ( isset( $entry['objectclass'][1] ) && $entry['objectclass'][1] == 'group' ) {
                    $this->context = 'self';
                    $dn = $entry['distinguishedname'][0];
                    $new_members[] = $this->get_group_members( $dn );
                    unset( $entries[$index] );
                }
            }
            foreach ( $new_members as $index => $entry ) {
                $count = $entries['count'] + $entry['count'];
                unset( $entries['count'] );
                unset( $entry['count'] );                
                $entries = array_merge( $entries, $entry );
                $entries['count'] = $count;
            }
            $this->context = $context_hold; 
            return $entries;
        } else {
            return false;
        }
    }


    /**
     * Get all the users of an OU
     * 
     * @param   string  $ou_dn   Set a custom or alternative ou_dn to the teacher or student context.
     * 
     * @return  array|bool  False if failed
     * 
     * @access  public
     * @since   3.11.0
     */

    public function get_ou_user_members( ?string $ou_dn = null ): array|bool {
        if ( @ldap_bind( $this->ldap_con, $this->dn, $this->dn_password ) ) {
            switch ( $this->context ) {
                case 'teachers':
                    $location = $this->sync_teachers_ou;
                    break;
                case 'students':
                    $location = $this->sync_students_ou;
                    break;
                default:
                    if ( !is_null( $ou_dn ) ) {
                        $location = $ou_dn;
                    } else {
                        echo "Invalid context selected";
                        return false;
                    }
            }
            $filter = "(&(objectCategory=user)(samaccountname=*))";
            $results = ldap_search( $this->ldap_con, $location, $filter );
            return ldap_get_entries( $this->ldap_con, $results );
        } else {
            return false;
        }
    }


    /**
     * Check if a specified group exists on the AD server
     * 
     * @param   string  $group  The DN of the group to be tests
     * 
     * @return  boolean
     * 
     * @access  public
     * @since   3.11.0
     */

    public function test_group_exists( string $group ): bool {
        if ( $group == '' || is_null( $group ) ) {
            return false;
        }
        $get_group_location = function ( $context ) {
            $hold = array_reverse( explode( ',', $context ) );
            $skip = false;
            foreach ( $hold as $i => $item ) {
                if ( $skip ) {
                    unset( $hold[$i] );
                    continue;
                }
                if ( substr ( $item, 0, 2 ) == 'OU' ) {
                    $skip = true;
                }
            }
            return implode( ',', array_reverse ( $hold ) );
        };
        if ( @ldap_bind( $this->ldap_con, $this->dn, $this->dn_password ) ) {
            $group_location = $get_group_location( $group );
            $filter = "(&(objectClass=group)(distinguishedName={$group}))";
            $results = @ldap_search( $this->ldap_con, $group_location, $filter );
            if ( !$results ) {
                return false;
            }
            $entries = @ldap_get_entries( $this->ldap_con, $results );
            if ( !$entries ) {
                return false;
            }
            return ( $entries['count'] > 0 ? true : false );
        } else {
            return false;
        }
    }

}
