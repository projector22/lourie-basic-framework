<?php

namespace LBF\Db\FieldAssets;

enum PrimaryKey {
    /**
     * Not Deprecated
     */
    case AUTO_INT;
    /**
     * Not Deprecated
     */
    case UUID;

    /**
     * @deprecated  4.0.0
     */
    case UUID_PLACEHOLDER;

    /**
     * @deprecated  4.0.0
     */
    case UUID_PLACEHOLDER2;
    /**
     * Not Deprecated
     */
    case MD5;
    /**
     * @deprecated  4.0.0
     */
    case MD5_CHAR;
    /**
     * Not Deprecated
     */
    case TXT;
}