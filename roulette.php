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

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/*
 * The application get your API steam key and will use it during the processus.
 * Then, it displays a form to the user expecting a steam id as an input.
 * The script get the game list and propose a new form making the sorting and picking easier.
 */

/**
 * Just launches everything.
 */
function getVueRoulette($completeWebpage=false){
    // First of all, get the steam API key.
    $steamAPIkey='';
    try {
        getAPIkey($steamAPIkey);
    } catch (Exception $e) {
        echo $e;
    }
    // We are ready to display the form.
    return displayForm($steamAPIkey, $completeWebpage);
}

/**
 * Retrieve the STEAM API developper key stored in the file ./steam_api_key
 * @param $steamAPIkey String Pointer to the string where this function put the API key.
 * @throws Exception
 */
function getAPIkey(&$steamAPIkey){
    if(file_exists(dirname(__FILE__) . '/steam_api_key_dev')){ // Get the dev API key (only for devs).
        try {
            $steamAPIkey = trim(file_get_contents(dirname(__FILE__) . '/steam_api_key_dev'));
        } catch (Exception $e){
            echo '<p>Can\'t acces to the API steam key file : ' . dirname(__FILE__) . '/steam_api_key_dev </p>';
            echo '<p>File is found but is unreadable.</p>';
        }
    } else if (file_exists('steam_api_key')) { // Get the user steam api key.
        try {
            $steamAPIkey = trim(file_get_contents(dirname(__FILE__) . '/steam_api_key'));
        } catch (Exception $e){
            echo '<p>Can\'t acces to the API steam key file : ' . dirname(__FILE__) . '/steam_api_key </p>';
            echo '<p>File is found but is unreadable.</p>';
        }
    } else { // No api key found.
        throw new Exception('Could not find any API key file in ' . dirname(__FILE__));
    }
}

/**
 * Builds the entire HTML/CSS/js code of the page. then displays it.
 * @param string $steamAPIkey
 */
function displayForm($steamAPIkey='', $completeWebpage=false){
    // todo make it less ugly (need help)

    /*
     * Now, we face a choice : do we have a user id to explore ?
     * - yes    -> grab the list and display the form
     * - no     -> display the form
     */
    $gameList=[];
    $completeSteamGameList=[];
    if( isset($_POST['steamId']) && $_POST['steamId'] ){
        $gameList = json_decode(getUserGames($steamAPIkey), true)['response']['games'];
        $completeSteamGameList = json_decode(file_get_contents('http://api.steampowered.com/ISteamApps/GetAppList/v0001/'), true)['applist']['apps']['app'];
    }

    $header = '<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Steam roulette</title>
        <style>
            body{
                margin-top: 5%;
                margin-left: 20%;
                margin-right: 20%;
                text-align: center;
            }
            
            #titleSteamRoulette{
                margin-bottom: 10%;
            }
            
            #linkToSteamTuto{
                font-size: x-small;
            }
        </style>
    </head>
    <body>';

    $form='
        <script>
            // Complete list of all steam games paired like this : {gameId: gameName)
            const steamCompleteList = {';
    foreach ($completeSteamGameList as $game) {
        $form .= '
        "' . $game['appid'] .'": "' . htmlspecialchars($game['name'], ENT_QUOTES) . '",';
    }
    $form .='
            };
            
            // All the games you own paired like this : {gameId:totalPlaytime}
            const ownedGames = {';
    foreach ($gameList as $game) {
        $form .= '
        "' . $game['appid'] .'": "' . $game['playtime_forever'] . '",';
    }

    $form .= '};
            /**
             * Updates a html block with data from the selected game.
             */
            function updateChosenGameView(gameid=0, gameName=\'\', timePlayed=0) {
                let codeHTML = \'<h4>\' + gameName + \'</h4>\';
                codeHTML += \'<p><img src="https://steamcdn-a.akamaihd.net/steam/apps/\' + gameid + \'/header.jpg" / alt="Picture of the game : \' + gameName + \'"></p>\';
                codeHTML += \'<p>Steam indicates you already played it for \' + timePlayed + \' hours.</p>\';
                const myGameBlock = document.getElementById(\'gameChosen\');
                myGameBlock.innerHTML = codeHTML;
            }
            
            /*
             * Displays an error when unable to reach steam servers.
             */
            function displaySteamConnectionError() {
                document.getElementById(\'errorBlock\').innerText = \'<p>Error while trying to reach steam API. Please, refresh the page.</p>\';
            }
            
            /*
             * Pick randomly a game from the user library.
             */
            function pickGame(firstLoad=false){
                if(Object.keys(steamCompleteList).length > 10 && Object.keys(ownedGames).length !== 0) {
                    //console.log("you own " + Object.keys(ownedGames).length + " games.");
                    const indexGameToPick = Math.floor(Math.random() * Object.keys(ownedGames).length);
                    let count = 0;
                    for(gameid in ownedGames) {
                        if(count === indexGameToPick) {
                            //console.log("chosen game id : " + gameid);
                            //console.log("chosen game time played : " + ownedGames[gameid]);
                            for(gameidcompleteList in steamCompleteList) {
                                if(gameidcompleteList==gameid) {
                                    //console.log("game name : " + steamCompleteList[gameidcompleteList]);
                                    // Finally, we have all informations required. Let\'s update the page.
                                    updateChosenGameView(gameid, steamCompleteList[gameidcompleteList], ownedGames[gameid]);
                                    break;
                                }
                            }
                            break;
                        }
                        count++;
                    }
                } else if(firstLoad=false) {
                    displaySteamConnectionError();
                }
            }
        </script>';

    // Ternary operator seems to not work when injected inside the html code. :/
    ( isset($_POST['steamId']) && $_POST['steamId']!='') ? $defaultValue = $_POST['steamId'] : $defaultValue = '';

    $form .= '
    <div id="steamRouletteWrapper" onload="pickGame();">
        <h1 id="titleSteamRoulette">Steam roulette</h1>
        
        <div id="errorBlock">
            
        </div>
        
        <form method="post" action="' . 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] . '">
            <h3>Please insert your user steam id :</h3>
            <input type="text" name="steamId" placeholder="steamid64" value="' . $defaultValue . '">
            <button type="submit">Send</button>
            <p id="linkToSteamTuto">
                <a href="https://steamcommunity.com/sharedfiles/filedetails/?l=english&id=209000244">How to get you steam id.</a>
            </p>
        </form>
        ';

    if (count($gameList) > 0){
        $form .= '<h5>Game :</h5>
        <div id="gameBlock">
            <button onclick="pickGame(true);">Pick a game</button>
            <div id="gameChosen">
            </div>
        </div>
        ';
    }
    $form .= '
    </div>';

    $footer = '
    </body>
</html>';
    if($completeWebpage){
        return $header . $form . $footer;
    }
    return $form;
}


/**
 * Returns all the game ids that a user owns on steam.
 * @param $steamAPIkey
 * @return false|string
 */
function getUserGames($steamAPIkey){
    $data='{}';
    if( isset($_POST['steamId']) && $_POST['steamId']){
        $data = file_get_contents('http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=' . $steamAPIkey . '&steamid=' . $_POST['steamId'] . '&format=json');
    }
    return $data;
}

/**
 * When given a certain game id, will return the data about the corresponding game.
 * @param string $steamAPIkey
 * @param string $steamGamiId
 * @return false|string
 */
function getGameInformation($steamAPIkey='', $steamGamiId=''){
    $gameData='{}';
    if($steamAPIkey!='' && $steamGamiId!=''){
        return file_get_contents('http://api.steampowered.com/ISteamUserStats/GetSchemaForGame/v2/?key=' . $steamAPIkey . '&appid=' . $steamGamiId);
    }
    return $gameData;
}