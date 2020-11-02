<?php

namespace WHMCS\Module\Addon\Mailerlite\Admin;

use WHMCS\Module\Addon\Mailerlite\Views\Views;
use WHMCS\Module\Addon\Mailerlite\Helpers\MailerliteHelperClass;
use WHMCS\Module\Addon\Mailerlite\Exceptions\MailerliteException;
use WHMCS\Module\Addon\Mailerlite\Helpers\ModuleHelperClass;
use WHMCS\Module\Addon\Mailerlite\Exceptions\DbException;

/**
 * Sample Admin Area Controller
 */
class Controller
{

    /**
     * Index action.
     *
     * @param array $vars Module configuration parameters
     *
     * @return string html string
     */
    public function index($vars)
    {
        // Get common module parameters
        $modulelink = $vars['modulelink']; // eg. addonmodules.php?module=addonmodule

        // if there is an active setting we are going straight to main settings page
        $activeSetting = (new ModuleHelperClass())->isActiveSetting();

        if ($activeSetting) {
            header('Location: ' . $modulelink . '&action=synchronizedlist');
            exit();
        }

        // pull html content from template file
        $viewString = file_get_contents(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'indexpage.tpl');

        // render view
        return (new Views($viewString, ['modulelink' => $modulelink], 'index'))
            ->render();
    }

    /**
     * Validate API key action
     *
     * @param array $vars Module configuration parameters
     * @return string html string
     */
    public function validate($vars)
    {
        $modulelink = $vars['modulelink'];

        if (!isset($_REQUEST['mailerlite-api-key'])) {
            header('Location: ' . $modulelink);
            exit();
        }

        try {
            // fetch groups fromm MailerLite API
            $groups = (new MailerliteHelperClass($_REQUEST['mailerlite-api-key']))->fetchAllGroups();

            // pull main page content from template file
            $viewString = file_get_contents(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'subscriptionlistpage.tpl');

            // proper NEXT action, if no groups are made we are going home
            $subsriptionBtn = (count($groups) > 0) ? true : false;

            // render view
            return (new Views($viewString, ['modulelink' => $modulelink, 'select' => $groups, 'subscriptionbtn' => $subsriptionBtn, 'mailerliteapikey' => $_REQUEST['mailerlite-api-key']], 'selectlist'))
                ->render();
        } catch (MailerliteException $e) {
            // we are staying on the same page (index)
            $viewString = file_get_contents(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'indexpage.tpl');

            // render index page with error message
            return (new Views($viewString, ['modulelink' => $modulelink, 'message' => $e->getMessage()], 'index', true))
                ->render();
        }
    }

    /**
     * Selected List action (Synchronized list)
     *
     * @param array $vars Module configuration parameters
     * @return string html string
     */
    public function synchronizedlist($vars)
    {

        $modulelink = $vars['modulelink'];
        $helper = new ModuleHelperClass();
        $data = $helper->prepareSubscripitonData($_REQUEST);

        if (count($data) === 0) {
            header('Location: ' . $modulelink);
            exit();
        }

        if (isset($_REQUEST['mailerlite-token'])) {
            // inserting data into DB, mailer settings table
            $helper->insertData($data);
        }

        // pull main page html content form template file
        $viewString = file_get_contents(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'synchronizedlistpage.tpl');

        return (new Views($viewString, ['modulelink' => $modulelink, 'selectedlist' => $data['list']['name'], 'selectedlistid' => $data['list']['id']], 'synchronizedlist'))->render();
    }

    /**
     * Disconnect mailerlite page
     *
     * @param array $vars Module configuration parameters
     * @return string html string
     */
    public function disconnect($vars)
    {
        $modulelink = $vars['modulelink'];
        if (!isset($_REQUEST['list'])) {
            header('Location: ' . $modulelink);
            exit();
        }
        
        $listId = filter_var($_REQUEST['list'], FILTER_SANITIZE_NUMBER_INT);
        $helper = new ModuleHelperClass();

        try {
            $helper->disconnect($listId);
            header('Location: ' . $modulelink);
            exit();
        } catch (DbException $e) {
            $data = $helper->prepareDataFromDb();

            if (count($data) === 0) {
                header('Location: ' . $modulelink);
                exit();
            }

            // pull main page html content form template file
            $viewString = file_get_contents(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'synchronizedlistpage.tpl');

            return (new Views($viewString, ['modulelink' => $modulelink, 'selectedlist' => $data['name'], 'selectedlistid' => $data['id'], 'message' => $e->getMessage()], 'synchronizedlist', true))->render();
        }
    }
}
