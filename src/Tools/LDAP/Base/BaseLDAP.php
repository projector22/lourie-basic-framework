<?php

namespace LBF\Tools\LDAP\Base;

use LBF\Tools\LDAP\LDAP;

abstract class BaseLDAP {

    public function __construct(protected LDAP $ldap) {
        $ldap->connect();
    }

}