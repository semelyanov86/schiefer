<?php

class PBXManager_Utils_Action extends PBXManager_Ajax_Action
{
    private $refuitypes = [10,51,52,53,57,58,59,68,73,75,81,76,78,80];

    public function process(Vtiger_Request $req)
    {
        return $this->_emit($this->getFields('Contacts'));
    }

    public function getFields($module, $parent = false)
    {
        $instance = Vtiger_Module_Model::getInstance($module);
        $fields = Vtiger_Field::getAllForModule($instance);
        $walkRefs = $parent !== false;
        $data = [];
        $refs = [];
        if (!$fields) {
            return [];
        }
        foreach ($fields as $f) {
            if (!$f) {
                continue;
            }
            if (!$walkRefs) {
                if (in_array($f->uitype, $this->refuitypes)) {
                    $vtField = Vtiger_Field_Model::getInstance($f->id);
                    $refs[] = [
                        'id' => $f->id,
                        'ref' => $vtField->getReferenceList(),
                        'parent' => $f->name,
                    ];
                }
            }
            $label = vtranslate($f->label);
            $data[] = [
                'id' => $f->id,
                'label' => $walkRefs
                    ? ($parent.':'.$label) 
                    : $label
            ];
        }

        if (!$walkRefs) {
            foreach ($refs as $ref) {
                $refMod = array_shift($ref['ref']);
                if (!$refMod || ($refMod == $module)) continue;
                $refFields = $this->getFields($refMod, $ref['parent']);
                //$data[$refMod] = $refFields;
                array_push($data, ...$refFields);
            }
        }

        return $data;
    }
}
