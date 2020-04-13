<?php
/**
 * Project : steam_roulette
 * File : roulette.php
 * PROJET steam roulette.
 * @throws Exception
 * @copyright Copyright (c) 2020, Pierre-Alexandre RACINE <patcha.dev at{@} gmail dot[.] com>
 * @date 04/04/2020
 * @link https://github.com/racine-p-a/steam_roulette
 * @author  Pierre-Alexandre RACINE <patcha.dev at{@} gmail dot[.] com>
 */

/*
 * The application get your API steam key and will use it during the processus.
 * Then, it displays a form to the user expecting a steam id as an input.
 * The script get the game list and propose a new form making the sorting and picking easier.
 */

function getVueRoulette(){
    // First of all, get the steam API key.
    $steamAPIkey='';
    $steamUserId='';
    try {
        getAPIkey($steamAPIkey);
    } catch (Exception $e) {
        echo $e;
    }
    //echo $steamAPIkey;

    /*
     * Now, we face a choice : do we have a user id to explore ?
     * - yes    -> grab the list and display the form
     * - no     -> display the form
     */

}


function getAPIkey(&$steamAPIkey){
    if(file_exists(dirname(__FILE__) . '/steam_api_key_dev')){ // Get the dev API key (only for devs).
        try {
            $steamAPIkey = trim(file_get_contents(dirname(__FILE__) . '/steam_api_key_dev'));
        } catch (Exception $e){
            echo '<p>Can\'t acces to the API steam fey file : ' . dirname(__FILE__) . '/steam_api_key_dev </p>';
            echo '<p>File is found but is unreadable.</p>';
        }
    } else if (file_exists('steam_api_key')) { // Get the user steam api key.
        try {
            $steamAPIkey = trim(file_get_contents(dirname(__FILE__) . '/steam_api_key'));
        } catch (Exception $e){
            echo '<p>Can\'t acces to the API steam fey file : ' . dirname(__FILE__) . '/steam_api_key </p>';
            echo '<p>File is found but is unreadable.</p>';
        }
    } else { // No api key found.
        throw new Exception('Could not find any API key file in ' . dirname(__FILE__));
    }
}

