<?php

namespace LBF\Auth;

use Throwable;

/**
 * Trait for working with user permissions. Designed to be attached to a user account class.
 * 
 * use LBF\Auth\UserAuth;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

trait UserAuth {

    /**
     * Property containing the relevant user permissions.
     * 
     * @var array   $user_permissions
     * 
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private array $user_permissions = [
        'is'  => [],
        'can' => [],
    ];


    /**
     * Set user permissions to this user object.
     * 
     * @param   array   $permissions    The permissions to add.
     * @param   bool    $id             Whether the permissions are user->is or user->can. Default: true (is).
     * 
     * @return  bool
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function set_permissions(array $permissions, bool $is = true): bool {
        $is_can = $is ? 'is' : 'can';
        $this->user_permissions[$is_can] = [];
        try {
            foreach ($permissions as $id => $permit) {
                $this->user_permissions[$is_can][$permit] = $id;
            }
            return true;
        } catch (Throwable) {
            return false;
        }
    }


    /**
     * Returns if a user may perform the parsed role.
     * 
     * @param   string  $permit The permission name.
     * 
     * @return  bool
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function is(string $permit): bool {
        return isset($this->user_permissions['is'][$permit]);
    }


    /**
     * Returns if a user can perform a specified permission.
     * 
     * @param   string  $permit The permission name.
     * 
     * @return  bool
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function can(string $permit): bool {
        return isset($this->user_permissions['can'][$permit]);
    }
}
