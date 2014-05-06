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
    
require 'BaseMethod.php';

class LoadItemsMethod extends BaseMethod {

    public function execute($params, &$result, &$error) {
        
        try {
            
            // Create database handler
            require('Database.php');
            $dbh = new Database();
            
            // Insert 3 test records
            $sth = $dbh->prepare('INSERT INTO items (title, cost) VALUES (?, ?)');
            $sth->execute(array('jeans', 100));
            $sth->execute(array('shoes', 150));
            $sth->execute(array('cap', 130));

        } catch (PDOException $e) {
            
            $error['code'] = 'DATABASE_ERROR';
            $error['message'] = $e->getMessage();
        }
    }
}