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

class GetOrdersMethod extends BaseMethod {

    public function execute($params, &$result, &$error) {

        $resultData = array();

        try {

            // Create database handler
            require('Database.php');
            $dbh = new Database();

            // Find the user
            $sth = $dbh->prepare('SELECT * FROM users WHERE token = ?');
            $sth->execute(array($params['token']));

            // If not found then return NOT_LOGGED_IN
            // SQLite does not support rowCount: http://stackoverflow.com/questions/12720648/php-sqlite-pdo-check-if-user-is-in-table
            //if ($sth->rowCount() == 0) {
            $userRow = $sth->fetch();

            if (!$userRow) {

                $error['code'] = 'NOT_LOGGED_IN';
                $error['message'] = 'Please login. Bad token.';

                // Otherwise proceed with the login
            } else {

                //Select last update_date
                $sth = $dbh->prepare('SELECT o.id order_id, o.date, oi.id item_id, i.title FROM orders o, order_items oi, items i WHERE o.user_id = ? AND o.id = oi.order_id AND oi.item_id = i.id ORDER BY o.id, oi.id');
                $sth->execute(array($userRow['id']));

                //setup array to hold information
                $orders = NULL; 

                //setup holders for the different types so that we can filter out the data
                $orderId = 0;
                $orderItemId = 0;

                //setup to hold our current index
                $orderIndex = -1;
                $orderItemIndex = -1;

                //Go through the rows
                while ($orderItemRow = $sth->fetch()) {

                    if ($orderId != $orderItemRow['order_id']) {
                        $orderIndex++;
                        $orderItemIndex = -1;
                        $orderId = $orderItemRow['order_id'];

                        //add the order
                        $orders[$orderIndex]['order_id'] = intval($orderItemRow['order_id']);
                        $orders[$orderIndex]['date'] = $orderItemRow['date'];

                        //setup the items array
                        $orders[$orderIndex]['items'] = array();
                    }

                    if ($orderItemId != $orderItemRow['item_id']) {
                        $orderItemIndex++;
                        $orderItemId = $orderItemRow['item_id'];

                        //setup the title array
                        $orders[$orderIndex]['items'][$orderItemIndex] = array();
                    }

                    //add the game to the current console and model
                    $orders[$orderIndex]['items'][$orderItemIndex][] = array(
                        'title' => $orderItemRow['title']
                    );
                }

                $resultData['orders'] = $orders;
            }
        } catch (PDOException $e) {

            $error['code'] = 'DATABASE_ERROR';
            $error['message'] = $e->getMessage();
        }

        $result = $resultData;
    }
}
