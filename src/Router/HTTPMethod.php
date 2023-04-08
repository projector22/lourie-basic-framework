<?php

namespace LBF\Router;

/**
 * Enum representing the various HTTP request methods possible.
 * 
 * use LBF\Router\HTTPMethod;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   4.0.0
 */

enum HTTPMethod {
    case GET;
    case POST;
    case PUT;
    case DELETE;
}