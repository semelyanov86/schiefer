<?php

class PBXManager_Config_Model
{
    /**
     * Init with data source fs/db
     */
    function __construct($adapter = false)
    {
        $this->db = empty($adapter)
            ? PearDatabase::getInstance()
            : $adapter;
    }

    /**
     * Retrive full config
     *
     * @return array
     */
    public function get()
    {
        return [
            'card' => $this->getCardDefaults(),
            'module' => $this->getModDefaults(),
        ];
    }

    /**
     * TODO specific key retrieval
     */
    public function __get($key)
    {
        // get full config and retrive value
        $svc = $this->getSvc();
    }

    public function getModOpts($key = false)
    {
        // TODO user options
        return $this->getModDefaults();
    }

    public function getCardOpts($key = false)
    {
        // TODO user options
        return $this->getCardDefaults();
    }

    /**
     * Vtiger properties required to run properly
     *
     * @return array
     */
    public function getGlobals()
    {
        $user = Users_Record_Model::getCurrentUserModel();
        $settings = PBXManager_Server_Model::getInstance();
        $version = Vtiger_Module::getInstance('PBXManager')->version;

        $clean = str_replace(
            ['http:', 'https:'],
            '',
            $settings->get('webappurl')
        );
        list($host, $port) = explode(':', $clean);
        $vtVersion = Vtiger_Version::current();
        return [
            // module version
            'version' => $version,
            // vt version TODO detect sp | org
            'vtmajor' => array_shift(explode('.', $vtVersion)),
            // active user extension
            'exten' => $user->get('phone_crm_extension'),
            // current connector host
            'host'  => $host,
            // current port
            'port'  => $port,
            // active user id
            'userid' => $user->id,
        ];
    }

    /**
     * Phonecall Card related defs
     *
     * @return array
     */
    public function getCardDefaults()
    {
        return [
            // display only customer/target phone number
            'onlyCustomer' => false,
            // max active cards
            'max'       => 2,
            // card display mechanics
            'queueType' => 'revolve',
            // display internal calls
            'internal'  => 'true',
            // seconds to remove card
            'hideDelay' => '3',
            // card buttons
            'buttons'   => 'close,toggle,info',
            // new entity actions
            'actions'   => 'Leads',
            // amount of related info
            'related'   => 'default',
            // lookup limit
            'maxRelated' => 5,
            // retrieve Assigned info. requires permissions/methods
            'allowAssigned' => false,
            // do not retrieve related
            'skipRelated' => true,
            // hide empty fields
            'skipEmpty' => true,
            // cache forward list
            'cacheFwd' => true,
            // localStorage for blocking, history, etc
            'useStorage' => false,
            'allowIcon' => true,
            // retrieve Assigned info. requires permissions/methods
            'allowOwner' => false,
            // allow user selection with textinput
            'allowAssign' => false,
            // enable Forward feature
            'allowForward' => true,
            // send pulse to keep session alive
            'enablePulse' => false,
        ];
    }

    /**
     * Module related defaults
     *
     * @return array
     */
    public function getModDefaults()
    {
        return [
            // Save only ones in CRM
            'strictSave'     => false,
            // audio tag option none, metadata, full
            'audioPreload'   => 'metadata',
            // Save internal calls
            'allowInternal'  => false,
            // skip call types
            'skipTypes' => ['internal', 'none'],
            // extension
            'extenMaxLength' => 6,
            // TODO Phone numbers manipulation
            'transform'      => '',
            'host'           => '0.0.0.0',
            // Enable websockets support
            'enableWS'       => true,
            // update customer on number detection
            'updateOnLookup' => true,
            // maintain single N2C flag for customer
            'singleN2C' => true,
        ];
    }

    /**
     * Return audio tag preload mode
     *
     * @return string
     */
    public function getPreload()
    {
        $opts = $this->getModDefaults();
        return $options['audioPreload'];
    }

    /**
     * Read configuration from file system
     *
     * @return json_decode result
     */
    public function fsConfig()
    {
        $cfg = 'modules/PBXManager/config.json';
        if (!file_exists($cfg)) {
            return false;
        }

        $json = file_get_contents($cfg);

        return json_decode($json, 1);
    }

    public function getSvc()
    {

    }
}

