<?php
/**
 * WHMCS SDK Sample Addon Module Hooks File
 *
 * Hooks allow you to tie into events that occur within the WHMCS application.
 *
 * This allows you to execute your own code in addition to, or sometimes even
 * instead of that which WHMCS executes by default.
 *
 * @see https://developers.whmcs.com/hooks/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

use WHMCS\Module\Addon\Mailerlite\Helpers\ModuleHelperClass;

add_hook('ClientEdit', 1, function(array $params) {
    (new ModuleHelperClass())->updateList($params, false);
});

add_hook('ClientAdd', 2, function(array $params) {
    (new ModuleHelperClass())->updateList($params);
});
