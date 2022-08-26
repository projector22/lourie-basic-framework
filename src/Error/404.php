<?php

use LBF\Img\SVGImages;

/**
 * Draw out the page that will display when there is a 404 error.
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.1.0
 * @since   3.9.1   Reworked and remade, new image & text
 * @since   3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                  Namespace changed from `Framework` to `LBF`.
 */

$image = html_path( SVGImages::error404->path() );
echo "<div class='container__404'>";
echo "<h3>Um... yeah</h3>";
echo "<img class='error_img' src='{$image}'>";
echo "<h1>404 - Page Not Found</h1>";
echo "</div>";