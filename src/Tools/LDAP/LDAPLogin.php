<?php

namespace LBF\Tools\LDAP;

use LBF\Tools\LDAP\Base\BaseLDAP;

final class LDAPLogin extends BaseLDAP {

    /**
     * Test an ldap sync with the assigned data
     * 
     * @return  boolean
     * 
     * @access  public
     * @since   LRS 3.1.0
     * @since   LBF 0.1.2-beta  Revamped
     */

    public function ldap_login(): bool {
        return $this->ldap->bind();
    }
}
