<?php

namespace LBF\Tools\LDAP;

use Exception;

/**
 * Various methods for performing and executing LDAP queries
 * 
 * use LBF\Tools\LDAP\LDAPHandler;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.1.0
 * @since   LRS 3.11.0  Moved to `Framework\Tools\LDAPHandler` and class renamed `LDAPHandler` from `LDAP`.
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                      Namespace changed from `Framework` to `LBF`.
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
     * @since   LRS 3.11.0
     */

    private $ldap_con;

    /**
     * The domain binding name of the LDAP server
     * 
     * @var string  $dn
     * 
     * @access  private
     * @since   LRS 3.1.0
     */

    private string $dn;

    /**
     * The domain binding password of the LDAP server
     * 
     * @var string  $dn_password
     * 
     * @access  private
     * @since   LRS 3.1.0
     */

    private string $dn_password;

    /**
     * The ip or domain address of the LDAP server
     * 
     * @var string  $ldap_server
     * 
     * @access  private
     * @since   LRS 3.1.0
     */

    private string $ldap_server;

    /**
     * The port being used by the LDAP server
     * 
     * @var string  $port
     * 
     * @access  private
     * @since   LRS 3.1.0
     */

    private string $port;

    /**
     * Contains the LDAP object data.
     * 
     * @var object|null $ldap_config
     * 
     * @access  private
     * @since   LBF 0.1.2-beta
     */

    private ?object $ldap_config;

    /**
     * Constructor method, things to do when the class is loaded.
     * If data is not specified, it will pull from the database
     * 
     * @param   string|null dn              The specified domain name.
     *                                      Default: null
     * @param   string|null dn_password     The specified domain dn_password.
     *                                      Default: null
     * @param   string|null ldap_server     The specified domain ldap_server.
     *                                      Default: null
     * @param   string|null port            The specified domain port.
     *                                      Default: null
     * @param   object|null $config_object  The object from which config data can be drawn.
     *                                      In LRS this is `new GeneralConfigData`.
     *                                      Default: null
     * 
     * @access  public
     * @since   LRS 3.1.0
     * @since   LRS 3.11.0  Removed param $search_ou
     * @since   LRS 3.28.0  Added param `$config_object`.
     * 
     * @deprecated  LBF 0.8.0-beta
     */

    public function __construct(
        ?string $dn = null,
        ?string $dn_password = null,
        ?string $ldap_server = null,
        ?string $port = null,
        ?object $config_object = null,
    ) {
        // Check if PHP_LDAP exists on the system
        $this->check_ldap();

        if (
            is_null($dn) &&
            is_null($dn_password) &&
            is_null($ldap_server) &&
            is_null($port) &&
            is_null($config_object)
        ) {
            throw new Exception("You must parse either LDAP details, or an object allowing for the searching of config data.");
        }

        if (is_null($config_object)) {
            $this->dn          = $dn;
            $this->dn_password = $dn_password;
            $this->ldap_server = $ldap_server;
            $this->port        = $port;
        } else {
            /**
             * @todo    This needs to be more general
             * 
             * @since   0.1.2-beta
             */
            $this->ldap_config = $config_object;
            $this->ldap_config->get_ldap_config();

            $fields = [
                'ldap_enabled', 'ldap_server', 'dn', 'dn_password', 'port', 'sync_teachers_by_ou', 'sync_teachers_by_group', 'sync_teachers_ou',
                'sync_teachers_group', 'delete_accounts_not_present_on_server', 'sync_students_by_ou', 'sync_students_by_group', 'sync_students_ou',
                'sync_students_group',
            ];

            foreach ($fields as $field) {
                if ($field == 'dn' && !is_null($dn)) {
                    $this->$field = $dn;
                    continue;
                }
                if ($field == 'dn_password' && !is_null($dn_password)) {
                    $this->$field = $dn_password;
                    continue;
                }
                if ($field == 'ldap_server' && !is_null($ldap_server)) {
                    $this->$field = $ldap_server;
                    continue;
                }
                if ($field == 'port' && !is_null($port)) {
                    $this->$field = $port;
                    continue;
                }
                $this->$field = $this->ldap_config->$field;
            }
        }

        $this->ldap_con = ldap_connect($this->ldap_server, $this->port);
        if (!$this->ldap_con) {
            echo "LDAP connection failed";
            die;
        }
        ldap_set_option($this->ldap_con, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($this->ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
    }


    /**
     * Test an ldap sync with the assigned data
     * 
     * @return  boolean true    If the sync works
     * @return  boolean false   If the sync fails
     * 
     * @access  public
     * @since   LRS 3.1.0
     * @since   LBF 0.1.2-beta  Revamped
     */

    public function ldap_login(): bool {
        return @ldap_bind($this->ldap_con, $this->dn, $this->dn_password);
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
     * @since   LRS 3.1.0
     */

    public function ldap_search(string $object_category = 'user', string $sam_account_name = '*', ?string $search_ou = null): array|bool {
        if (@ldap_bind($this->ldap_con, $this->dn, $this->dn_password)) {
            // Would like to make this more dynamic
            $filter = "(&(objectCategory=$object_category)(samaccountname=$sam_account_name))";
            if (!is_null($search_ou)) {
                $results = ldap_search($this->ldap_con, $search_ou, $filter);
            } else {
                $results = ldap_search($this->ldap_con, $this->search_ou, $filter);
            } // if check for custom search ou
            return ldap_get_entries($this->ldap_con, $results);
        } else {
            return false;
        }
    }


    /**
     * Check that LDAP is enabled and exists
     * 
     * @access  public
     * @since   LRS 3.6.1
     */

    private function check_ldap(): void {
        if (!function_exists('ldap_connect')) {
            echo "<pre>";
            throw new Exception("LDAP not enabled");
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
     * @since   LRS 3.11.0
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
     * @since   LRS 3.11.0
     * @since   LRS 3.12.1  Added recursiveness to get members of groups within the defined group
     */

    public function get_group_members(?string $group_dn = null): array|bool {
        $get_group_location = function ($context) {
            // Search through the whole of the Domain Controller
            $hold = array_reverse(explode(',', $context));
            foreach ($hold as $i => $item) {
                if (substr($item, 0, 2) !== 'DC') {
                    unset($hold[$i]);
                }
            }
            return implode(',', array_reverse($hold));
        };
        if (@ldap_bind($this->ldap_con, $this->dn, $this->dn_password)) {
            switch ($this->context) {
                case 'teachers':
                    $context = $this->sync_teachers_group;
                    $group_location = $get_group_location($context);
                    break;
                case 'students':
                    $context = $this->sync_students_group;
                    $group_location = $get_group_location($context);
                    break;
                default:
                    if (!is_null($group_dn)) {
                        $context = $group_dn;
                        $group_location = $get_group_location($context);
                    } else {
                        echo "Invalid context selected";
                        return false;
                    }
            }
            $filter = "(memberof={$context})";
            $results = ldap_search($this->ldap_con, $group_location, $filter);
            $entries = ldap_get_entries($this->ldap_con, $results);
            $context_hold = $this->context;
            $new_members = [];
            foreach ($entries as $index => $entry) {
                if (isset($entry['objectclass'][1]) && $entry['objectclass'][1] == 'group') {
                    $this->context = 'self';
                    $dn = $entry['distinguishedname'][0];
                    $new_members[] = $this->get_group_members($dn);
                    unset($entries[$index]);
                }
            }
            foreach ($new_members as $index => $entry) {
                $count = $entries['count'] + $entry['count'];
                unset($entries['count']);
                unset($entry['count']);
                $entries = array_merge($entries, $entry);
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
     * @since   LRS 3.11.0
     */

    public function get_ou_user_members(?string $ou_dn = null): array|bool {
        if (@ldap_bind($this->ldap_con, $this->dn, $this->dn_password)) {
            switch ($this->context) {
                case 'teachers':
                    $location = $this->sync_teachers_ou;
                    break;
                case 'students':
                    $location = $this->sync_students_ou;
                    break;
                default:
                    if (!is_null($ou_dn)) {
                        $location = $ou_dn;
                    } else {
                        echo "Invalid context selected";
                        return false;
                    }
            }
            $filter = "(&(objectCategory=user)(samaccountname=*))";
            $results = ldap_search($this->ldap_con, $location, $filter);
            return ldap_get_entries($this->ldap_con, $results);
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
     * @since   LRS 3.11.0
     */

    public function test_group_exists(string $group): bool {
        if ($group == '' || is_null($group)) {
            return false;
        }
        $get_group_location = function ($context) {
            $hold = array_reverse(explode(',', $context));
            $skip = false;
            foreach ($hold as $i => $item) {
                if ($skip) {
                    unset($hold[$i]);
                    continue;
                }
                if (substr($item, 0, 2) == 'OU') {
                    $skip = true;
                }
            }
            return implode(',', array_reverse($hold));
        };
        if (@ldap_bind($this->ldap_con, $this->dn, $this->dn_password)) {
            $group_location = $get_group_location($group);
            $filter = "(&(objectClass=group)(distinguishedName={$group}))";
            $results = @ldap_search($this->ldap_con, $group_location, $filter);
            if (!$results) {
                return false;
            }
            $entries = @ldap_get_entries($this->ldap_con, $results);
            if (!$entries) {
                return false;
            }
            return ($entries['count'] > 0 ? true : false);
        } else {
            return false;
        }
    }
}
