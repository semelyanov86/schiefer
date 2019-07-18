<?php

require_once 'libraries/adodb/adodb-active-record.inc.php';

/**
 * TODO namespace it
 * Store pbx otions in database as key value
 */
class PBXManager_Opts_Model
{
    /**
     * Simple static getter
     *
     * @param str $key option name
     *
     * @return bool | str
     */
    static function get($key)
    {
        $ctl = self::instance();
        $found = $ctl->load('name = ?', [$key]);
        if (!$found) return false;

        $v = $ctl->value;
        $ctl->reset();

        return $v;
    }

    /**
     * Simple static setter
     *
     * @param str $k option key
     * @param str $v option value
     *
     * @return ADODB saving result 0/1/2 fail/saved/updated
     */
    static function set($k, $v)
    {
        $ctl = self::instance();
        $lookup = $ctl->load('name = ?', [$k]);
        $done = false;

        $ctl->name = $k;
        $ctl->value = $v;
        $done = $ctl->save();
        $ctl->reset();

        return $done;
    }

    /**
     * Remove key from db
     *
     * @param str $k key
     *
     * @return delete result
     */
    static function drop($k)
    {
        $ctl = self::instance();
        $lookup = $ctl->load('name = ?', [$k]);
        $done = $ctl->delete();
        $ctl->reset();

        return $done;
    }

    /**
     * Singleton
     *
     * @return ADODB_Active_Record instance
     */
    static function instance()
    {
        static $provider = false;
        if ($provider) {
            return $provider;
        }

        $db = PearDatabase::getInstance();
        $provider = new ADOdb_Active_Record(
            'vtiger_pbxopts',
            $db->database
        );

        return $provider;
    }
}
