<?php

namespace LBF\Config;

/**
 * Enum defining different modes or states the app may operate under.
 * 
 * use LBF\Config\Employment;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0
 */

enum AppMode: int {
    case PRODUCTION = 0;
    case MAINTENANCE = 1;
    case DEVELOPEMENT = 2;
    case ARCHIVE = 3;
}
