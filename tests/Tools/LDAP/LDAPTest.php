<?php

declare(strict_types=1);

namespace Test\Tools\LDAP;

use LBF\Tools\LDAP\LDAP;
use PHPUnit\Framework\TestCase;

final class LDAPTest extends TestCase {
    public ?LDAP $ldap;

    protected function setUp(): void {
        $this->ldap = new LDAP(
            dn: 'ou=cheese',
            password: 'abc123',
            server: 'ldap://test.example.net',
            // port: 389
        );
    }
    protected function tearDown(): void {
        $this->ldap = null;
    }


    public function testInit(): void {
        $this->assertTrue(class_exists(LDAP::class, true));

        $this->assertEquals($this->ldap->get_dn(), 'ou=cheese');
    }

}
