<?php

namespace LBF\Tools\LDAP;

use LBF\Errors\IO\LDAPConnect;
use LBF\Errors\IO\LDAPDisabled;
use LDAP\Connection;

final class LDAP {

    public readonly Connection|false $conn; /** @todo figure out type */

    public function __construct(
        private readonly string $dn,
        private readonly string $password,
        private readonly string $server,
        private readonly int $port = 389,
    ) {
        if (!$this->ldap_enabled()) {
            throw new LDAPDisabled("Please install and enable the LDAP php extension.");
        }
    }

    public function __destruct() {
        if ($this->conn !== false) {
            /**
             * @see https://www.php.net/manual/en/function.ldap-unbind.php
             */
            @ldap_unbind($this->conn);
        }
    }


    public function connect(): void {
        $this->conn = ldap_connect($this->server, $this->port);
        if (!$this->conn) {
            throw new LDAPConnect("Failed to connect to the LDAP Server with the provided credentials.");
        }
        ldap_set_option($this->conn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    }


    public function bind(): bool {
        return @ldap_bind($this->conn, $this->dn, $this->password) ?? false;
    }

    public function ldap_enabled(): bool {
        return extension_loaded('ldap');
    }

    public function get_dn(): string {
        return $this->dn;
    }
    public function get_password(): string {
        return $this->password;
    }
    public function get_server(): string {
        return $this->server;
    }
    public function get_port(): string {
        return $this->port;
    }
}
