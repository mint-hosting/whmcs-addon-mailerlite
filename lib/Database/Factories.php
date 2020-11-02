<?php

namespace WHMCS\Module\Addon\Mailerlite\Database;

use WHMCS\Database\Capsule;

class Factories
{

    /**
     * Factory state - it could be active or inactive
     *
     * @var string
     */
    protected $state;

    /**
     * Module name
     *
     * @var string
     */
    protected $moduleName;

    public function __construct($state = '')
    {
        $this->state = $state;
        $this->moduleName = 'mailerlite';
    }

    /**
     * Making Setting data in memory like
     *
     * @param array $params
     * @return array
     */
    public function make($params = [])
    {
        $data = [];
        switch ($this->state) {
            case 'active':
                $data = $this->makeActiveSetting($params);
                break;
            
            case 'inactive':
                $data = $this->makeInactiveSetting($params);
                break;

            case '':
                $data = $this->makeActiveSetting($params);
                break;
            default:
                break;
        }

        return $data;
    }

    /**
     * Creating Setting data, inserting into database
     *
     * @param array $params
     * @return array
     */
    public function create($params = [])
    {
        $data = $this->make($params);
        $insert = $this->insertSetting($data);

        return $data;
    }

    /**
     * Insert setting into db
     *
     * @param array $data
     * @return bool
     */
    private function insertSetting($data)
    {
        return Capsule::table('mod_mailerlite_settings')->insert($data);
    }

    /**
     * Create setting @_REQUEST array of data
     *
     * @param string $action controller action
     * @param  array $params
     * @return array
     */
    public function makeRequestData($action, $params = [])
    {
        $data = [];
        switch ($action) {
            case 'synchronizedlist':
                $data = [
                    "module" => $this->moduleName,
                    "action" => $action,
                    "token" => $this->randomString(),
                    "primary_list" => $this->randomNumber() . '_' . $this->randomString(),
                    "mailerlite-token" => $this->randomString()
                ];
                break;
            case 'disconnect':
                $data = [
                    "module" => $this->moduleName,
                    "action" => $action,
                    "list" => $this->randomNumber(),
                ];
                break;
        }

        return $this->setData($data, $params);
    }

    /**
     * Making active setting data (db row)
     *
     * @param array $params
     * @return array
     */
    private function makeActiveSetting($params)
    {
        $data = $this->defaultSetOfData(1);
        return $this->setData($data, $params);
    }

    /**
     * Set final data values
     *
     * @param array $data array of default values
     * @param array $params array send from tests
     * @return array
     */
    private function setData($data, $params)
    {
        if (count($params) === 0) {
            return $data;
        }

        $arrayDiff = array_diff($data, $params);
        $result = array_merge($arrayDiff, $params);

        return $result;
    }

    /**
     * Making inactive setting data (db row)
     *
     * @param array $params
     * @return array
     */
    private function makeInactiveSetting($params)
    {
        $data = $this->defaultSetOfData(0);
        return $this->setData($data, $params);
    }

    /**
     * Prepare default values for mailerlite setting
     *
     * @param int $status
     * @return array
     */
    private function defaultSetOfData($status)
    {
        $apiKey = $this->randomString();
        $listId = $this->randomNumber();
        $time = (new \DateTime())->format('Y-m-d H:i:s');

        return [
            'api_key' => $apiKey,
            'list_id' => $listId,
            'status' => $status,
            'created_at' => $time,
            'updated_at' => $time
        ];
    }

    /**
     * Default set of whmcs controller data
     *
     * @return array
     */
    public function defaultVarsArray()
    {
        return [
            "module" => "mailerlite",
            "modulelink" => "addonmodules.php?module=mailerlite",
            "version" => "1.0",
            "access" => "1",
            "_lang" => [
                "variable_name" => "Translated language string."
            ]
        ];
    }

    /**
     * Random string generator
     *
     * @return string
     */
    private function randomString()
    {
        return md5(time());
    }

    /**
     * Random number generator
     *
     * @return void
     */
    private function randomNumber()
    {
        return rand(999, 999999);
    }
}
