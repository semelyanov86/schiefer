<?php

/**
 * Provide frontend extensions
 */
class PBXManager_Card_Model
{
    const EXTROOT = 'modules/PBXManager/extends/';
    public static $extensions = [];

    /**
     * Append processor to registry
     *
     * @param string                    $name controller name
     * @param PBXManager_Extender_Model $ctl  controller instance
     *
     * @return void
     */
    public static function register($ctl)
    {
        $name = get_class($ctl);
        self::$extensions[$name] = $ctl;
    }

    /**
     * Execute all registered processors
     *
     * @param mixed $args some data
     *
     * @return mixed some results
     */
    public static function walk($args = [])
    {
        $results = [];
        foreach (self::$extensions as $label => $controller) {
            $results[] = $controller->process($args);
        }

        return $results;
    }

    /**
     * Scan folder for new items
     *
     * @return array
     */
    public static function load()
    {
        $data = [];
        foreach (glob(self::EXTROOT . '*.php') as $f) {
            $fname = basename($f, '.php');
            $className = 'Extender' . $fname;
            if (!file_exists($f)) {
                // anomaly
                $data[] = $f . ' not found';
                continue;
            }
            include_once($f);
            if (!class_exists($className)) {
                // Invalid include
                $data[] = $className . ' not found';
                continue;
            }
            // constructor autoreg
            $extender = new $className;

            // TODO register initial params
            $args = (new ReflectionClass($extender))
                ->getConstructor()
                ->getParameters();

            $params = [];
            foreach ($args AS $param) {
                $params[] = $param->name;
            }
            $info = empty($params)
                ? 'None'
                : implode(',', $params);
            $data[] = $f . ': ' . $info;
        }

        return empty($data)?[]:$data;
    }
}
