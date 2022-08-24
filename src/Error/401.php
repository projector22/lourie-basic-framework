<?php
/**
 * Draw out the page that will display when there is a 403 error.
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.9.1   Reworked and remade
 */

$image = BASE_URL . 'src/img/401.svg';
echo "<div class='container__401'>";
echo "<h3>Can't find your creds here mate...</h3>";
echo "<img class='error_img' src='{$image}'>";
echo "<h1>401 - Unauthorized</h1>";
echo "</div>";