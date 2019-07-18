<?php

class PBXManager_SmartLookup_Action extends Vtiger_BasicAjax_Action
{
    public $fio = 'cf_1127';

    public function process(Vtiger_Request $req)
    {
        $term = $req->get('search_value');
        $this->limit = (int)$req->get('limit', 10);
        $term = trim($term);
        if (empty($term)) {
            return $this->_emit([]);
        }

        $this->_emit($this->lookup($term));
    }

    public function lookup($term)
    {
        $method = is_numeric($term)
            ? 'searchByNum'
            : 'searchByName';

        return $this->{$method}($term);
    }

    public function searchByName($fio)
    {
        return $this->fetch(
            $this->fio,
            "%{$fio}%"
        );
    }

    public function searchByNum($serial)
    {
        return $this->fetch(
            'lastname',
            "%{$serial}%"
        );
    }

    /**
     * Simple crmentity join
     * props used: limit
     */
    public function fetch($field, $value)
    {
        $fields = [
            'crmid',
            'contactid',
            'lastname',
            'contact_no',
            $this->fio
        ];
        $db = PearDatabase::getInstance();
        $db->database->SetFetchMode(2);
        $sql = 'SELECT ' . implode(',', $fields)
            . ' FROM vtiger_contactdetails
            LEFT JOIN vtiger_contactscf USING (contactid)
            LEFT JOIN vtiger_crmentity ON crmid = contactid
                WHERE deleted = 0 AND ' . $field . ' LIKE ?
            ORDER BY crmid DESC
            LIMIT ' . $this->limit;
        $result = $db->pquery($sql, [$value]);
        $rows = $db->num_rows($result);
        if ($rows == 0) {
            return [];
            return [$db->database->ErrorMsg()];
        }

        return $this->_toResults($result->GetAll());
    }

    public function _toResults($queryData)
    {
        return array_map(
            function ($row) {
                return [
                    'label' => implode(' ', [
                        $row['lastname'],
                        trim($row[$this->fio])
                    ]),
                    'value' => $row[$this->fio],
                    'id'    => $row['crmid'],
                ];
            },
            $queryData
        );
    }

    /**
     * response wrapper
     */
    public function _emit($x, $code = false)
    {
        $res = new Vtiger_Response;
        $res->setEmitType(Vtiger_Response::$EMIT_JSON);
        if ($code) {
            $res->setError($code, $x);
        } else {
            $res->setResult($x);
        }
        $res->emit();
    }
}
