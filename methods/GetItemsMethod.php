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

require 'methods'.DIRECTORY_SEPARATOR.'BaseMethod.php';
require 'database'.DIRECTORY_SEPARATOR.'Database.php';

class GetItemsMethod extends BaseMethod {

    public function execute($params, &$result, &$error) {

        $resultData = NULL;
        
        try {

            // Create database handler
            $dbh = new Database();

            //Select last update_date
            $sth = $dbh->prepare('SELECT i.* FROM items i');
            $sth->execute();

            $resultData['items'] = array();

            while ($row = $sth->fetch()) {

                $item = array(
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'cost' => $row['cost']
                );
                array_push($resultData['items'], $item);
            }
        } catch (PDOException $e) {

            $error['code'] = 'DATABASE_ERROR';
            $error['message'] = $e->getMessage();
        }

        // Return nil if no records were found
        if (empty($resultData['items'])) {
            
            $resultData['items'] = NULL;
        }
        
        $result = $resultData;
    }
}
