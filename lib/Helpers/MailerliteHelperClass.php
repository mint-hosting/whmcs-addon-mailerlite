<?php

namespace WHMCS\Module\Addon\Mailerlite\Helpers;

use WHMCS\Module\Addon\Mailerlite\Exceptions\MailerliteException;

require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

class MailerliteHelperClass {

    /**
     * MailerLite API Key
     *
     * @var string
     */
    private $epiKey;

    /**
     * MailerLite Client
     *
     * @var obj
     */
    protected $mailerlite;

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
        $this->mailerlite = new \MailerLiteApi\MailerLite($this->apiKey);
    }

    /**
     * Fetching all list from Mailerlite service
     *
     * @return array
     */
    public function fetchAllGroups() {
        $groupsApi = $this->mailerlite->groups();
        $groups = $groupsApi->get();

        if (isset($groups[0]->error)) {
            throw new MailerliteException("API Key appears to be malformed. Please double check entry and try again.", 401);
        }

        $preparedGroups = [];
        $groupsCount = count($groups);

        if ($groupsCount > 0) {
            for ($i = 0; $i < $groupsCount; $i++) {
                $preparedGroups[] = ['id' => $groups[$i]->id, 'name' => $groups[$i]->name];
            }
        }

        return $preparedGroups;
    }

    /**
     * Fetching single group by id
     *
     * @param int $id
     * @return obj
     */
    public function fetchSingleGroup($id)
    {
        $groupsApi = $this->mailerlite->groups();
        $singleGroup = $groupsApi->find($id);

        return $singleGroup;
    }

    /**
     * Adding client to saved mailerlite list as active user
     *
     * @param int $groupId
     * @param array $params
     * @return object
     */
    public function addToTheGroup($groupId, $params)
    {
        $groupsApi = $this->mailerlite->groups();
        return $groupsApi->addSubscriber($groupId, $params);
    }

    /**
     * Removing client from saved mailelite list
     *
     * @param int $groupId group id
     * @param string $email client email
     * @return object
     */
    public function removeFromTheGroup($groupId, $email)
    {
        $subscribersApi = $this->mailerlite->subscribers();
        $subscriber = $subscribersApi->find($email);

        if (isset($subscriber->error)) {
            return $subscriber;
        }

        $groupsApi = $this->mailerlite->groups(); 

        return $groupsApi->removeSubscriber($groupId, $subscriber->id);
    }

}
