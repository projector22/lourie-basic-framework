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

    public function get_group_members(string $group_dn): array|bool {
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
        if ($this->ldap->bind()) {
            $context = $group_dn;
            $group_location = $get_group_location($context);

            $filter = "(memberof={$context})";
            $results = ldap_search($this->ldap->conn, $group_location, $filter);
            $entries = ldap_get_entries($this->ldap->conn, $results);
            // $context_hold = $this->context;
            $new_members = [];
            foreach ($entries as $index => $entry) {
                if (isset($entry['objectclass'][1]) && $entry['objectclass'][1] == 'group') {
                    // $this->context = 'self';
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
            // $this->context = $context_hold;
            return $entries;
        }
        return false;
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

    public function get_ou_user_members(string $location): array|false {
        if ($this->ldap->bind()) {
            $filter = "(&(objectCategory=user)(samaccountname=*))";
            $results = ldap_search($this->ldap->conn, $location, $filter);
            return ldap_get_entries($this->ldap->conn, $results);
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
     */

    public function test_group_exists(string $group): bool {
        if ($group == '') {
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
}
