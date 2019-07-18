<?php

class ExtenderContact extends PBXManager_Extender_Model
{
    function __construct($phone = false, $id = false)
    {
        parent::__construct();
    }

    function process($data)
    {
        return 'Contact processor';
    }
}
