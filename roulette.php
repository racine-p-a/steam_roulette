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
 *
 */
function getVueRoulette(){
    // First of all, get the steam API key.
    $steamAPIkey='';
    try {
        getAPIkey($steamAPIkey);
    } catch (Exception $e) {
        echo $e;
    }
    // We are ready to display the form.
    displayForm($steamAPIkey);
}

/**
 * @param $steamAPIkey
 * @throws Exception
 */
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

/**
 * @param string $steamAPIkey
 */
function displayForm($steamAPIkey=''){
    // todo : add https://steamcommunity.com/sharedfiles/filedetails/?l=french&id=209000244
    // todo : presentation, explanation and context

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

    $form='<html>
    <head>
        <title>Steam roulette</title>
        <script type="text/javascript">
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

            function pickGame(){
                if(Object.keys(steamCompleteList).length !== 0 && Object.keys(ownedGames).length !== 0) {
                    //console.log("you own " + Object.keys(ownedGames).length + " games.");
                    const indexGameToPick = Math.floor(Math.random() * Object.keys(ownedGames).length);
                    let count = 0;
                    for(gameid in ownedGames) {
                        if(count === indexGameToPick) {
                            console.log("chosen game id : " + gameid);
                            console.log("chosen game time played : " + ownedGames[gameid]);
                            for(gameidcompleteList in steamCompleteList) {
                                if(gameidcompleteList==gameid) {
                                    console.log("game name : " + steamCompleteList[gameidcompleteList]);
                                    break;
                                }
                            }
                            break;
                        }
                        count++;
                    }
                }
            }
        </script>
    </head>';

    $form .= '
    <body onload="pickGame();">
        <h1>Steam roulette</h1>
        
        <form method="post" action="' . 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] . '">
            <h3>Please insert your user steam id</h3>
            <input type="text" name="steamId">
            <button type="submit">Send</button>
        </form>
        ';

    if (count($gameList) > 0){
        //var_dump(count($gameList));
        $gameChosen = $gameList[array_rand($gameList)];
        //var_dump($gameChosen);
        $form .= '<h5>Game :</h5>
        <div id="gameBlock">
            <button onclick="pickGame();">Pick a game</button>
            <div id="gameChosen">
            </div>
        </div>
        ';

    }

    $form .= '
    </body>
</html>';
    echo $form;
}


/**
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


function getGameInformation($steamAPIkey='', $steamGamiId=''){
    // http://api.steampowered.com/ISteamUserStats/GetSchemaForGame/v2/?key=AC9C5E9BD8BA183B5F1CDD160F5689ED&appid=702670
    $gameData='{}';
    if($steamAPIkey!='' && $steamGamiId!=''){
        return file_get_contents('http://api.steampowered.com/ISteamUserStats/GetSchemaForGame/v2/?key=' . $steamAPIkey . '&appid=' . $steamGamiId);
    }
    return $gameData;
}