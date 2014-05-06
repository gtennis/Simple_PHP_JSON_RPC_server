<?php

/**
 * Simple PHP JSON-RPC server implementation
 *
 * @package		Simple PHP JSON-RPC server implementation
 * @author		Gytenis Mikulenas
 * @copyright	Copyright (c) 2014, Gytenis Mikulenas
 * @license		The MIT License (MIT)
 
 Copyright (c) 2014 Gytenis Mikulenas
 
 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:
 
 The above copyright notice and this permission notice shall be included in all
 copies or substantial portions of the Software.
 
 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 SOFTWARE.
 
 * @since		Version 1.0
 * @link
 * @filesource
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
    
// Check for configs
require 'config.php';
if (!$config) {
    
    die('Config file was not found');
}

// Response template
$responseData = array('result'=>NULL, 'error'=>NULL, 'id'=>NULL);

// Get request body
$requestBody = file_get_contents('php://input');

// Check is correct request method
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    
    $responseData['error']['code'] = 'BAD_HTTP_REQUEST_METHOD';
    $responseData['error']['message'] = 'Bad HTTP request method ('.$_SERVER['REQUEST_METHOD'].'). POST method should be used.';
    $responseJson = json_encode($responseData); 
    die($responseJson);
}

// Decode request. Fetch rrequest body as associate array
$requestData= json_decode($requestBody, true);

// Extract request parameters
$params = $requestData['params'];

// Check api key
$api_key_param = $params['api_key'];
    
if (empty($api_key_param) | $api_key_param != $config['api_key']){
    
    $responseData['error']['code'] = 'BAD_API_KEY';
    $responseData['error']['message'] = 'Bad API key.';
    $responseJson = json_encode($responseData);
    die($responseJson);
}

// Extract needed to perform method
$method = $requestData['method'];

// Check if file exists
$fileExists = FALSE;

switch ($method) {
    
    case 'GetItems':
    case 'LoginUser' :
    case 'CreateUser':
    case 'AddToCart':
    case 'GetCartItems':
    case 'CreateOrder':
    case 'GetOrders':
    case 'ClearDb':
    case 'LoadItems':
        
        $fileExists = file_exists($method.'Method'.'.php');
        break;

    // Check if unknown method
    default:

        $responseData['error']['code'] = 'METHOD_DOES_NOT_EXISTS';
        $responseData['error']['message'] = 'Method does not exist ('.$method.').';
        $responseJson = json_encode($responseData); 
        die($responseJson);
        break;
}

// Check if method file exists
if (!$fileExists){
    
    $responseData['error']['code'] = 'METHOD_FILE_DOES_NOT_EXISTS';
    $responseData['error']['message'] = 'Method file does not exist ('.$method.').';
        $responseJson = json_encode($responseData); 
        die($responseJson); 
}

// TODO: perform input checking
// ...

if (empty($responseData['result'])) {
    
    $responseData['result'] = NULL;
    
} else {
    
    $responseData['result'] = array();
}

// Prepare method
include $method.'Method'.'.php';
$methodClass = $method.'Method'; 
$method = new $methodClass;

// Execute method
$method->execute($params, $responseData['result'], $responseData['error']);

// Prepare and return response
$responseJson = json_encode($responseData); 
header('Content-type: application/json');
header('HTTP/1.1 200');
die($responseJson);