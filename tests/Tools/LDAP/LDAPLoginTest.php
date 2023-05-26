<?php

declare(strict_types=1);

namespace Test\Tools\LDAP;

use LBF\Tools\LDAP\LDAP;
use LBF\Tools\LDAP\LDAPLogin;
use PHPUnit\Framework\TestCase;

final class LDAPLoginTest extends TestCase {
    public ?LDAP $ldap;

    protected function setUp(): void {
        $this->ldap = new LDAP(
            dn: 'ou=cheese',
            password: 'abc123',
            server: 'ldap://test.example.net',
            // port: 389
        );
    }
    

    public function testInit(): void {
        $this->assertTrue(class_exists(LDAPLogin::class, true));
    }
}
