<?php

/**
 * Blueprint for the Card extender
 */
abstract class PBXManager_Extender_Model
{
    function __construct()
    {
        PBXManager_Card_Model::register($this);
    }

    /**
     * Result as string?
     */
    abstract function process($args);

    /**
     * Result as Data (object/array)
     */
    //abstract function getData($args);
}
