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

// todo : tutorial about installing and using it.

try {
    $mySteamRoulette =  getVueRoulette(true);
} catch (Exception $e) {
    echo $e;
}

echo $mySteamRoulette;
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