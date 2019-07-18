<?php

/**
 * Class to work with phonelookup directly
 */
class PBXManager_ListExt_Action extends PBXManager_Ajax_Action
{
    public function process(Vtiger_Request $req)
    {
        $this->_emit($this->extens());
    }

    /**
     * Extend results with additional data
     *
     * @param arr $results reference to lookup data
     *
     * @return null
     */
    public function extens()
    {
        $db = PearDatabase::getInstance();
        $exts = $db->query(
            "SELECT user_name, phone_crm_extension FROM vtiger_users
                WHERE phone_crm_extension > 0"
        );

        $data = [];
        if ($db->num_rows($exts) == 0) return $data;

        while ($r = $db->fetch_array($exts)) {
            $data[$r['user_name']] = $r['phone_crm_extension'];
        }

        return $data;
    }
}
