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

class AddToCartMethod extends BaseMethod {

    public function execute($params, &$result, &$error) {

        try {

            // Create database handler
            require('Database.php');
            $dbh = new Database();

            // Perform input checking
            // ...
            // Find the item
            $sth = $dbh->prepare('SELECT * FROM items WHERE id = ?');
            $sth->execute(array($params['item_id']));
            $itemRow = $sth->fetch();

            // Check if item was found
            if (!$itemRow) {

                $error['code'] = 'BAD_ITEM';
                $error['message'] = 'This item does not exist (item_id)';
            } else {

                // Find the user
                $sth = $dbh->prepare('SELECT * FROM users WHERE token = ? AND id = ?');
                $sth->execute(array($params['token'], $params['user_id']));
                $userRow = $sth->fetch();

                // If not found then return NOT_LOGGED_IN
                //if ($sth->rowCount() == 0) {
                if (!$userRow) {

                    $error['code'] = 'NOT_LOGGED_IN';
                    $error['message'] = 'Please login. Bad user_id or token.';

                    // Else continue
                } else {

                    // Start transaction
                    $dbh->beginTransaction();

                    // Add to bag
                    $sth = $dbh->prepare('INSERT INTO user_cart (user_id, item_id) VALUES (?, ?)');
                    $sth->execute(array($params['user_id'], $params['item_id']));

                    // Save
                    $dbh->commit();
                }
            }
        } catch (PDOException $e) {

            $error['code'] = 'DATABASE_ERROR';
            $error['message'] = $e->getMessage();
        }
    }
}
