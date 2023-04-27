<?php

namespace LBF\Db\FieldAssets;

/**
 * Enum of various available PRIMARY_KEYS
 * 
 * use LBF\Db\FieldAssets\PrimaryKey;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.7.0-beta
 */

enum PrimaryKey {
    /**
     * Auto number INT.
     */
    case AUTO_INT;

    /**
     * UUID / GUID
     */
    case UUID;

    /**
     * Same spacing as a UUID, but requires manual generation as a VARCHAR.
     * 
     * @deprecated  4.0.0
     */
    case UUID_PLACEHOLDER;

    /**
     * Same spacing as a UUID, but requires manual generation as a CHAR.
     * 
     * @deprecated  4.0.0
     */
    case UUID_PLACEHOLDER2;

    /**
     * Spacing for an MD5 as a VARCHAR.
     */
    case MD5;

    /**
     * Spacing for an MD5 as a CHAR.
     * 
     * @deprecated  4.0.0
     */
    case MD5_CHAR;

    /**
     * A general spaced text key.
     */
    case TXT;
}
