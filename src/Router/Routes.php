<?php

namespace LBF\Router;

/**
 * Basic enum to indicate which routing task should be done be the application.
 * 
 * use LBF\Router\Routes;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

enum Routes {
    // Static
    case CLI;
    case HTTP;
    case API;

    // Wildcart
    case DOWNLOAD;
    case PDF;
}
