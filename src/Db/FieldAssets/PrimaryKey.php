<?php

namespace LBF\Db\FieldAssets;

enum PrimaryKey {
    case AUTO_INT;
    case UUID;
    case UUID_PLACEHOLDER;
    case MD5;
    case MD5_CHAR;
    case TXT;
}