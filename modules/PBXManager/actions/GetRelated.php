<?php

/**
 * Action to retrieve project specific data
 */
class PBXManager_GetRelated_Action extends PBXManager_Ajax_Action
{
    /**
     * skip fields with empty values
     */
    public $skipEmpty = true;

    /**
     * TODO config:skip users processing
     */
    public $skipOwners = true;

    /**
     * TODO config:skip subrecord
     * be replaced with Extenders
     */
    public $skipRelated = true;

    /**
     * return raw field value
     */
    public $raw = false;

    /**
     * logging
     */
    public $msg = [];

    /**
     * resulting entities list
     */
    public $entities = [];
    public $users = [];

    /**
     * override
     */
    function process(Vtiger_Request $req)
    {
        $response = [];

        $phone = $req->get('phone');
        if (!$phone) {
            return $this->_emit('No phone specified', 11);
        }

        $samePhone = $this->getByPhone($phone);
        if (empty($samePhone)) {
            return $this->_emit([]);
        }

        $this->getRelated($samePhone);

        $this->_emit(array_values($this->entities));
    }

    /**
     * Fill entities array
     * Do not add twice
     * $id => [
     *   link
     *   label
     *   owner
     *   entity specific
     * ]
     *
     * @param arr $ids phone lookup results
     *
     * @return void
     */
    function getRelated($ids)
    {
        if (empty($ids)) return false;

        foreach ($ids as $crm) {
            $id = $crm['crmid'];
            if (array_key_exists($id, $this->entities)) continue;
            $this->entities[$id] = $this->getByCustomer($id);
        }
    }

    /**
     * pbx phone lookup
     *
     * @param str $number phone
     *
     * @see PBXManager_Module_Model::lookup
     * @return arr []
     *   crmid label deleted smownerid creatorid
     */
    function getByPhone($number)
    {
        $pbxModule = Vtiger_Module_Model::getInstance('PBXManager');

        return $pbxModule->lookup($number, false, 5);
    }

    /**
     * Load entity info + cache
     * Single depth relation
     *
     * @param int  $id   crm id
     * @param bool $skip recursion controller
     *
     * @return arr related data
     */
    function getByCustomer($id, $skip = false)
    {
        if (empty($id)) return false;

        if (array_key_exists($id, $this->entities)) {
            return $this->entities[$id];
        }

        $record = $this->safeGet($id);
        if (!$record) {
            $this->msg[] = 'Unable to get record ' . $id;
            return false;
        }

        $modName = $record->getModuleName();

        $data = [];
        $data['id'] = $id;
        $data['link'] = $record->getDetailViewUrl();
        $data['label'] = $record->getDisplayName();
        $data['setype'] = $modName;

        switch ($modName) {
            case 'Contacts':
                $data['props'] = $this->processContact($record);
                $subacc = $record->get('account_id');
                if (!$this->skipRelated && $subacc && !$skip) {
                    $data['related'] = $this->getByCustomer($subacc, true);
                }
            break;
            case 'Accounts':
                $data['props'] = $this->processAccount($record);
                $subcon = $record->get('ownership');
                if (!$this->skipRelated && $subcon && !$skip) {
                    $data['related'] = $this->getByCustomer($subcon, true);
                }
            break;
            case 'Leads':
                $data['props'] = $this->processLead($record);
            break;
            default:
                $data['props'] = $record->getData();
        }

        if (!$this->skipOwners) {
            $data['owner'] = $this->getUserData($record->get('assigned_user_id'));
        }

        return $data;
    }

    function processContact($contact)
    {
        $fields = [
            'mobile',
            'email',
	    'cf_1137',
            /*
            'contact_no',
            'phone',
            'record_id',
            'record_module',
            'firstname',
            'lastname',
            'account_id',
            */
        ];

        return $this->extract($contact, $fields);
    }

    function processAccount($account)
    {
        $fields = [
            'email1',
            /*
            'phone',
            'accountname',
            'account_no',
            'record_module',
            */
        ];

        return $this->extract($account, $fields);
    }

    function processLead($rec)
    {
        $fields = [
            'description',
            'organization',
        ];

        return $this->extract($rec, $fields);
    }

    /**
     * Retrieve record fields
     *
     * @param Vtiger_Record_Model $record instance
     * @param arr                 $fields map
     *
     * @return arr [[label => str, value => mixed],]
     */
    function extract($record, $fields)
    {
        $data = [];
        foreach ($fields as $x) {
            $v = $record->get($x);
            if ($this->skipEmpty && empty($v)) continue;

            if ($this->raw) {
                $data[$x] = $v;
                continue;
            }

            $field = $record->getField($x);
            $lbl = $x;
            $val = $v;
            if ($field) {
                $lbl = vtranslate($field->get('label'));
                $val = $field->getDisplayValue($v);
            }

            $data[$x] = [
                'label' => $lbl,
                'value' => $val,
            ];
        }

        return $data;
    }

    /**
     * Process user info and cache it
     * TODO permissions to view owners
     *
     * @param int $uid crm user id
     *
     * @return arr
     */
    function getUserData($uid)
    {
        if (array_key_exists($uid, $this->users)) {
            return $this->users[$uid];
        }

        // TODO v7 v6 methods
        //$user = Users_Record_Model::getInstanceFromPreferenceFile($uid);
        $user = Vtiger_Record_Model::getInstanceById($uid, 'Users');
        $data = [
            'id' => $uid,
            'link' => $user->getDetailViewUrl(),
            'label' => $user->getDisplayName(),
            'props' => [
                'email1' => $user->get('email1'),
                'exten'  => $user->get('phone_crm_extension'),
            ]
        ];

        $this->users[$uid] = $data;

        return $data;
    }
}
