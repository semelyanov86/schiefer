<?php

class PBXManager_PhoneLookup_Model
{
    public function getStats()
    {
        $db = PearDatabase::getInstance();
        $db->database->setFetchMode(2);
        $sql = "SELECT
            count(*) ttl,
            count(DISTINCT fnumber) uniqs,
            count(DISTINCT crmid) ids,
            (SELECT count(*) FROM vtiger_pbxmanager_phonelookup
            INNER JOIN vtiger_crmentity USING (crmid)
            WHERE deleted = 1) deleted,
            (SELECT count(*) FROM vtiger_pbxmanager_phonelookup
            INNER JOIN vtiger_leaddetails ON crmid = leadid
            WHERE converted = 1) converted,
            count(IF(setype = 'Accounts', 1, null)) accs,
            count(IF(setype = 'Contacts', 1, null)) contacts,
            count(IF(setype = 'Leads', 1, null)) leads
          FROM vtiger_pbxmanager_phonelookup";
        $result = $db->query($sql);
        if (!$result) {
            return false;
        }

        return $db->fetch_array($result);
    }
}
