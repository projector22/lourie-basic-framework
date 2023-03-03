<?php

namespace LBF\Auth;

use Throwable;

trait UserAuth {

    private array $user_permissions = [
        'is'  => [],
        'can' => [],
    ];

    public function set_permissions( array $permissions, bool $is = true ): bool {
        $is_can = $is ? 'is' : 'can';
        $this->user_permissions[$is_can] = [];
        try {
            foreach( $permissions as $id => $permit ) {
                $this->user_permissions[$is_can][$permit] = $id;
            }
            return true;
        } catch ( Throwable ) {
            return false;
        }
    }

    public function is( string $permit ): bool {
        return isset( $this->user_permissions['is'][$permit] );
    }

    public function can( string $permit ): bool {
        return isset( $this->user_permissions['can'][$permit] );
    }


    public function set_is( array $is ): void {}
    public function set_can( array $can ): void {}
}