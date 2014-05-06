<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class BaseMethod {

    abstract public function execute($params, &$result, &$error);

    public function prepare($sql) {

        $statement = parent::prepare($sql);
        $statement->setFetchMode(PDO::FETCH_ASSOC);

        return $statement;
    }
}
