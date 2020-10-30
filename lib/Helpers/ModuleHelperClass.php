<?php 

namespace WHMCS\Module\Addon\Mailerlite\Helpers;

use WHMCS\Module\Addon\Mailerlite\Database\Migrations;
use WHMCS\Database\Capsule;
// use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\Module\Addon\Mailerlite\Helpers\MailerliteHelperClass;
use WHMCS\Module\Addon\Mailerlite\Exceptions\DbException;

/**
 * Helper class 
 * All addiotional method used in module
 */
class ModuleHelperClass
{

    /**
     * Handling migration response during activation process
     *
     * @param array $response array of migration responses
     * @return array 
     */
    public function handleActivateMigrationsResponse($response)
    {
        $errors = array_values(
            array_filter(
                $response, 
                function ($el) {
                    return $el['status'] === 'error';
                }
            )
        );
    
        $description = (count($errors) > 0) ? 'Unable to run migrations. Error messages: ' : 'Migrations are run successfully';
        $status = (count($errors) > 0) ? 'error' : 'success';
    
        if (count($errors) > 0) {
            // if we have one table migration failed, we are dropping all
            // Check migrations script and try again
            $migrations = new Migrations();
            $migrations->dropTables();
    
            $errorCount = count($errors);
            for ($i = 0; $i < $errorCount; $i++) {
                $description .= $errors[$i]['description'];
                $description .= ($i === $errorCount - 1) ? '.' : ', ';
            }
        }
    
        return [
            'status' => $status,
            'description' => $description
        ];
    }

    /**
     * Handling migration response during deactivation process
     *
     * @param array $response array of migration responses
     * @return array
     */
    public function handleDeactivateMigrationsResponse($response)
    {
        $errors = array_values(
            array_filter(
                $response, 
                function ($el) {
                    return $el['status'] === 'error';
                }
            )
        );
    
        $description = (count($errors) > 0) ? 'Unable to drop tables. Error messages: ' : 'Tables are dropped successfully';
        $status = (count($errors) > 0) ? 'error' : 'success';
    
        if (count($errors) > 0) {
    
            $errorCount = count($errors);
            for ($i = 0; $i < $errorCount; $i++) {
                $description .= $errors[$i]['description'];
                $description .= ($i === $errorCount - 1) ? '.' : ', ';
            }
        }
    
        return [
            'status' => $status,
            'description' => $description
        ];
    }

    /**
     * Fetching active mailerlite settings from db
     *
     * @return array
     */
    public function fetchActiveMailerliteSetting()
    {
        return Capsule::table('mod_mailerlite_settings')->where('status', 1)->get();
    }

    /**
     * Checking for active setting
     *
     * @return boolean
     */
    public function isActiveSetting()
    {
        $setting = $this->fetchActiveMailerliteSetting();
        return (count($setting) > 0) ? true : false;
    }

    /**
     * Preparing subscription list id and name data
     *
     * @param string $list %id_name%
     * @return array
     */
    public function prepareSubscripitonData($data)
    {
        if (!isset($data['mailerlite-token'])) {
            $response = $this->prepareDataFromDb();
        } else {
            $api = $data['mailerlite-token'];

            $explodedList = explode('_', $data['primary_list']);

            $id = (isset($explodedList[0])) ? $explodedList[0] : 0;
            $name = (isset($explodedList[1])) ? $explodedList[1] : '';
            $list = ['id' => $id, 'name' => $name];

            $response = ['api' => $api, 'list' => $list];
        }

        return $response;
    }

    /**
     * Fetching single single list data from db and mailerlite api
     *
     * @return array
     */
    public function prepareDataFromDb()
    {
        $setting = $this->fetchActiveMailerliteSetting();

        if (count($setting) === 0) {
            return [];
        }

        $api = $setting[0]->api_key;
        $singleList = (new MailerliteHelperClass($api))->fetchSingleGroup($setting[0]->list_id);

        return ['api' => $api, 'list' => ['id' => $setting[0]->list_id, 'name' => $singleList->name]];
    }

    /**
     * Insert setting into db
     *
     * @param array $data array of data to insert
     * @return bool
     */
    public function insertData($data)
    {
        $isActive = $this->isActiveSetting();

        if ($isActive) {
            throw new DbException("There is already active setting.", 500);
        }

        $time = (new \DateTime())->format('Y-m-d H:i:s');

        $data = [
            'api_key' => $data['api'],
            'list_id' => $data['list']['id'],
            'status' => 1,
            'created_at' => $time,
            'updated_at' => $time
        ];

        return Capsule::table('mod_mailerlite_settings')->insert($data);
    }

    /**
     * Deactivating mailerlite setting
     *
     * @param int $list mailerlite list id
     * @return bool
     */
    public function disconnect($list)
    {
        $isActive = $this->isActiveSetting();

        if (!$isActive) {
            throw new DbException("There is already active setting.", 500);
        }

        $data = [
            'status' => 0,
            'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ];
        
        $update = Capsule::table('mod_mailerlite_settings')->where('list_id', $list)->where('status', 1)->update($data);

        if (!$update) {
            throw new DbException("Something happend, setting is not updated.", 500);
        }

        return $update;
    }

    /**
     * Wrapper method for handling updating/adding new user to the saved group
     *
     * @param array $params client data
     * @param boolean $new used for determing new/add action
     * @return object response from MailerLite API
     */
    public function updateList($params, $new = true)
    {
        $setting = $this->fetchActiveMailerliteSetting();

        if (count($setting) === 0) {
            return false;
        }

        $api = $setting[0]->api_key;
        $data = $this->prepareDataForMailerliteApi($params);

        if ($new) {
            $isEmailOptIn = $this->fetchMarketingEmailsOptInValue($params['userid']);
            return ($isEmailOptIn) ? 
                (new MailerliteHelperClass($api))->addToTheGroup($setting[0]->list_id, $data) : false;
        }

        return ($params['isOptedInToMarketingEmails']) ? 
            (new MailerliteHelperClass($api))->addToTheGroup($setting[0]->list_id, $data) :
            (new MailerliteHelperClass($api))->removeFromTheGroup($setting[0]->list_id, $params['email']);

    }

    /**
     * Preparing data for MailerLite API
     *
     * @param array $params client data
     * @return array
     */
    private function prepareDataForMailerliteApi($params)
    {
        return [
            'email' => $params['email'],
            'name' => $params['firstname'],
            'type' => 'active',
            'fields' => [
                'surname' => $params['lastname'],
                'company' => $params['companyname'],
                'city' => $params['city']
            ]
        ];
    }

    /**
     * Fetching marketing emails opt in value when new client is added
     *
     * @param int $id
     * @return bool
     */
    private function fetchMarketingEmailsOptInValue($id)
    {
        $command = 'GetClientsDetails';
        $postData = array(
            'clientid' => $id,
            'stats' => false,
        );
        // $adminUsername = 'ADMIN_USERNAME'; // Optional for WHMCS 7.2 and later
    
        $result = localAPI($command, $postData);

        return $result['marketing_emails_opt_in'];
    }
}
