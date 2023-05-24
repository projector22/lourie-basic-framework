<?php

namespace LBF\Tools\LDAP;

use LBF\Tools\LDAP\Base\BaseLDAP;

final class LDAPSearch extends BaseLDAP {

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
     * 
     * @deprecated  LBF 0.8.0-beta
     */

    public function ldap_search(string $object_category = 'user', string $sam_account_name = '*', ?string $search_ou = null): array|bool {
        if ($this->ldap->bind()) {
            // Would like to make this more dynamic
            $filter = "(&(objectCategory={$object_category})(samaccountname={$sam_account_name}))";
            if (!is_null($search_ou)) {
                $results = ldap_search($this->ldap->conn, $search_ou, $filter);
            }
            return ldap_get_entries($this->ldap->conn, $results);
        }
        return false;
    }


    /**
     * Perform a search, by parsing the OU and filter string.
     * 
     * @param   string  $ou     The OU in which to search.
     * @param   string  $filter The filer string with which to seach. Examples:
     *                          - `(&(objectClass=group)(distinguishedName=myGroup))`
     *                          - `(&(objectCategory=user)(samaccountname=myUserName))`
     * 
     * @return  array|false
     * 
     * @see https://wiki.mozilla.org/Mozilla_LDAP_SDK_Programmer%27s_Guide/Searching_the_Directory_With_LDAP_C_SDK
     * @see http://www.faqs.org/rfcs/rfc4515.html
     * 
     * @access  public
     * @since   LBF 0.8.0-beta
     */
    public function search(string $ou, string $filter): array|false {
        $results = ldap_search($this->ldap->conn, $ou, $filter);
        return ldap_get_entries($this->ldap->conn, $results);
    }


    /**
     * Find the user specified or all users within a nominated OU.
     * 
     * @param   string  $ou                 The OU in which to search.
     * @param   string  $sam_account_name   The LDAP attribute `sAMAccountName` which is tied to a user account. 
     *                                      Default '*' which searches for all users within the OU.
     * 
     * @return  array|false
     * 
     * @access  public
     * @since   LBF 0.8.0-beta
     */

    public function find_users(string $ou, string $sam_account_name = '*'): array|false {
        if ($this->ldap->bind()) {
            $filter = "(&(objectCategory=user)(samaccountname={$sam_account_name}))";
            return $this->search($ou, $filter);
        }
        return false;
    }


    /**
     * Find the group specified or all groups within a nominated OU.
     * 
     * @param   string  $ou     The OU in which to search.
     * @param   string  $cn     The LDAP attribute `cn` which is tied to a specific group.
     *                          Default '*' which searches for all groups within the OU.
     * 
     * @return  array|false
     * 
     * @access  public
     * @since   LBF 0.8.0-beta
     * 
     */

    public function find_groups(string $ou, string $cn = '*'): array|false {
        if ($this->ldap->bind()) {
            $filter = "(&(objectCategory=group)(cn={$cn}))";
            return $this->search($ou, $filter);
        }
        return false;
    }


    /**
     * Find the OU specified or all Organizational Units within an nominated OU.
     * 
     * @param   string  $ou     The OU in which to search.
     * @param   string  $name   The LDAP attribute `ou` which is tied to a specific Organizational Unit.
     *                          Default '*' which searches for all OUs within the OU.
     * 
     * @return  array|false
     * 
     * @access  public
     * @since   LBF 0.8.0-beta
     */

    public function find_ous(string $ou, string $name = '*'): array|false {
        if ($this->ldap->bind()) {
            $filter = "(&(objectCategory=organizationalUnit)(ou={$name}))";
            return $this->search($ou, $filter);
        }
        return false;
    }


    /**
     * Get all the members of a group
     * 
     * @param   string  $group_dn   Set a custom or alternative group_dn to the teacher or student context
     * 
     * @return array|false
     * 
     * @access  public
     * @since   LRS 3.11.0
     * @since   LRS 3.12.1      Added recursiveness to get members of groups within the defined group
     * @since   LBF 0.8.0-beta  Renamed from `get_group_members` to `find_group_members`
     */

    public function find_group_members(string $group_dn): array|false {
        if ($this->ldap->bind()) {
            $filter = "(memberof={$group_dn})";
            $entries = $this->search($this->get_group_context_location($group_dn), $filter);

            // $context_hold = $this->context;
            $new_members = [];
            foreach ($entries as $index => $entry) {
                if (isset($entry['objectclass'][1]) && $entry['objectclass'][1] == 'group') {
                    // $this->context = 'self';
                    $dn = $entry['distinguishedname'][0];
                    $new_members[] = $this->find_group_members($dn);
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
            // $this->context = $context_hold;
            return $entries;
        }
        return false;
    }


    /**
     * Get all the members of a group
     * 
     * @param   string  $group_dn   Set a custom or alternative group_dn to the teacher or student context
     * 
     * @return array|false
     * 
     * @access  public
     * @since   LRS 3.11.0
     * @since   LRS 3.12.1  Added recursiveness to get members of groups within the defined group
     * 
     * @deprecated  LBF 0.8.0-beta -> RENAME `find_group_members`
     */

    public function get_group_members(string $group_dn): array|false {
        return $this->find_group_members($group_dn);
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
     * 
     * @deprecated  LBF 0.8.0 -> USE `find_users` instead.
     */

    public function get_ou_user_members(string $location): array|false {
        return $this->find_users($location);
    }

    /**
     * @todo build this
     */


    // public function user_exists(string $user): bool {}


    /**
     * Check if a specified group exists.
     * 
     * @param   string  $group  The `distinguishedName` for the group being searched for.
     * 
     * @return  bool
     * 
     * @access  public
     * @since   LBF 0.8.0-beta
     */

    public function group_exists(string $group): bool {
        if (empty($group)) {
            return false;
        }
        if ($this->ldap->bind()) {
            $group_location = $this->get_group_location($group);
            $filter = "(&(objectClass=group)(distinguishedName={$group}))";
            $results = @ldap_search($this->ldap->conn, $group_location, $filter);
            if (!$results) {
                return false;
            }
            $entries = @ldap_get_entries($this->ldap->conn, $results);
            if (!$entries) {
                return false;
            }
            return ($entries['count'] > 0 ? true : false);
        }
        return false;
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
     * 
     * @deprecated  LBF 0.8.0 -> USE `group_exists` instead.
     */

    public function test_group_exists(string $group): bool {
        return $this->group_exists($group);
    }


    private function get_group_location(string $group): string {
        $hold = array_reverse(explode(',', $group));
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
    }


    private function get_group_context_location(string $context): string {
        // Search through the whole of the Domain Controller
        $hold = array_reverse(explode(',', $context));
        foreach ($hold as $i => $item) {
            if (substr($item, 0, 2) !== 'DC') {
                unset($hold[$i]);
            }
        }
        return implode(',', array_reverse($hold));
    }
}
