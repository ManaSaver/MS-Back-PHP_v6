<?php

use Controllers\MySQLController;
use Controllers\ResponseController;


function info($vars)
{
    $mysql = new MySQLController();
    $response = new ResponseController();

    //$mysql->getSingleItem('3b093c40-2ab6-4b18-8549-63524aac0d16');
    //$mysql->getBranch('8c2001c6-58a5-4d3f-b5c3-b750ddcdf98a', true);
    $mysql->getBranch(null, true);

    $response->handleMySQLResult($mysql);
    $response->send();
}

function getAllItems($vars) 
{
	$mysql = new MySQLController();
	$response = new ResponseController();

    $mysql->getBranch(null, true);

    $response->handleMySQLResult($mysql);
    $response->send();
}

function getOneItem($vars)
{
    $mysql = new MySQLController();
    $response = new ResponseController();

    $mysql->getSingleItem($vars['uuid']);

    if ($mysql->hasErrors()) {
        $response->handleMySQLResult($mysql);
        $response->send();
        return null;
    }

    $isCategory = false;

    // Якщо це категорія i вона не остання:
    if ($mysql->result[0]['type'] == 'category') {
        if(is_array($mysql->result[0]['tags'])) {
            if (!in_array('last', $mysql->result[0]['tags'])) {
                // довантажую до неї лише дочірні категорії:
                $isCategory = true;
            }
        }
    }

    $mysql->getBranch($mysql->result[0]['uuid'], $isCategory);

    $response->handleMySQLResult($mysql);
    $response->send();

}

function breadCrumbs($vars)
{
    $mysql = new MySQLController();
    $response = new ResponseController();

    $mysql->getBreadCrumbs($vars['uuid']);
    $mysql->result = array_reverse($mysql->result); // тут би хук заюзати!

    $response->handleMySQLResult($mysql);
    $response->send();
}


function putOneItem($vars)
{
    $mysql = new MySQLController();
    $response = new ResponseController();

    $mysql->getSingleItem($vars['uuid']);

    if ($mysql->hasErrors()) {
        $response->handleMySQLResult($mysql);
        $response->send();
        return null;
    }

    $mysql->updateItem($vars['uuid'], ResponseController::readRequestData());

    $response->handleMySQLResult($mysql);
    $response->send();

}


function postItem($vars)
{

    $mysql = new MySQLController();
    $response = new ResponseController();

    $mysql->createItem(ResponseController::readRequestData());

    $response->handleMySQLResult($mysql);
    $response->send();

}


function deleteItems($vars)
{
    $request = ResponseController::readRequestData();

    $mysql = new MySQLController();
    $response = new ResponseController();

    $mysql->deleteItem($request);

    foreach($mysql->result as $key => $recordToDestroy) {
        //uuid
        $mysql->result[$key] = $recordToDestroy['uuid'];
    }

    $response->handleMySQLResult($mysql);

    $response->send();

}























function postItems($vars) 
{
    $request = ResponseController::readRequestData();

    $mysql = new MySQLController();
	$response = new ResponseController();

    $mysql->getSingleItem($vars['uuid']);

    if (count($mysql->result) == 0) {
        $response->httpStatus(404);
        $response->responseData([]);
        $response->send();
        http_response_code(404);
        die();
    }

    $response->sqlQueries($mysql->sql);
    $response->responseData($mysql->result);

	$response->send();
}

function putItems($vars)
{
    $request = ResponseController::readRequestData();

    $mysql = new MySQLController();
	$response = new ResponseController();

	$response->responseData(['putItems' => $vars, 'raw' => $request]);
	$response->send();
}



