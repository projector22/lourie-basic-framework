<?php

namespace LBF\Db\FieldAssets;

enum PrimaryKey {
    case AUTO_INT;
    case UUID;
    case MD5;
    case MD5_CHAR;
    case TXT;
}