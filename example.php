<?php
/**
 * Project : steam_roulette
 * File : example.php
 * @author  Pierre-Alexandre RACINE <patcha.dev at{@} gmail dot[.] com>
 * @copyright Copyright (c) 2020, Pierre-Alexandre RACINE <patcha.dev at{@} gmail dot[.] com>
 * @license http://www.gnu.org/licenses/lgpl.html
 * @date 13/04/2020
 * @link
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once dirname(__FILE__) . '/roulette.php';


/*
 * For a complete web page ?
 * If true, this will generate the entire HTML code for a single page working by itself.
 * If false, this will generate HTML code in a div tag that you can embbed in any webpage (just think to apply some 
 * CSS style on it)
 */
$completeWebPage=true;

try {
    $mySteamRoulette =  getVueRoulette(true);
} catch (Exception $e) {
    echo $e;
}

// If you chose to generate a signle satnd-alone web page.
echo $mySteamRoulette;

// If not, here is an exemple of how integrate. (remember to comment the precedent line containing « echo $mySteamRoulette; »)
/*
echo '<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Steam roulette</title>
    </head>
    <body>
        <div>' . $mySteamRoulette . '</div>
    </body>
</html>';
*/