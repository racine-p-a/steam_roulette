<?php
/**
 * Project : steam_roulette
 * File : example.php
 * @author  Pierre-Alexandre RACINE <patcha.dev at{@} gmail dot[.] com>
 * @copyright Copyright (c) 2019, Pierre-Alexandre RACINE <patcha.dev at{@} gmail dot[.] com>
 * @license http://www.gnu.org/licenses/lgpl.html
 * @date 13/04/2020
 * @link
 */

require_once dirname(__FILE__) . '/roulette.php';

try {
    getVueRoulette();
} catch (Exception $e) {
}