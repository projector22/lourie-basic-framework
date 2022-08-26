<?php
/**
 * Draw out the page that will display when there is a 403 error.
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.9.1   Reworked and remade
 * @since   3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                  Namespace changed from `Framework` to `LBF`.
 */

use LBF\Img\SVGImages;

$image = html_path( SVGImages::error401->path() );
echo "<div class='container__401'>";
echo "<h3>Can't find your creds here mate...</h3>";
echo "<img class='error_img' src='{$image}'>";
echo "<h1>401 - Unauthorized</h1>";
echo "</div>";