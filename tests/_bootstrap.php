<?php

// This is the bootstrap for PHPUnit testing.

if (!defined('WHMCS')) {
    define('WHMCS', true);
}

if (!defined('ROOTDIR')) {
    define('ROOTDIR', '/code/whmcs');
}

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../vendor/autoload.php';

use WHMCS\Module\Addon\Mailerlite\Database\Factories;
use WHMCS\Database\Capsule;

// Include the WHMCS module.
require_once __DIR__ . '/../mailerlite.php';

/**
 * Include Factories class
 */
require_once __DIR__ . '/../lib/Database/Factories.php';

/**
 * making settting data in memory like
 *
 * @param array $params
 * @param string $state
 * @return void
 */
function make($params = [], $state = '')
{
    return (new Factories($state))->make($params);
}

/**
 * Creating a setting row in database
 *
 * @param array $params
 * @param string $state
 * @return array
 */
function create($params = [], $state = '')
{
    return (new Factories($state))->create($params);
}

/**
 * Making @_REQUEST data in memory like
 *
 * @param string $state
 * @param array $params
 * @return array
 */
function makeRequestData($action, $params = [])
{
    return (new Factories())->makeRequestData($action, $params);
}

function createDefaultVarsArray()
{
    return (new Factories())->defaultVarsArray();
}

/**
 * Setting up the database table 
 * create connection, set as global connaction, dropping the table, creating the table
 *
 * @return object WHMCS\Database\Capsule instance
 */
function setUpDbTable()
{
    $capsule = new Capsule();

    $capsule->addConnection(
        array(
            'driver'    => $_SERVER['DB_DRIVER'],
            'host'      => $_SERVER['DB_HOST'],
            'database'  => $_SERVER['DB_NAME'],
            'username'  => $_SERVER['DB_USER'],
            'password'  => $_SERVER['DB_PASSWORD'],
            'charset'   => $_SERVER['DB_CHARSET'],
            'collation' => $_SERVER['DB_COLLATION'],
            'prefix'    => '',
        ),
        $_SERVER['DB_CONNECTION']
    );

    $capsule->setAsGlobal();

    dropTable($capsule);
    createTable($capsule);

    return $capsule;
}

/**
 * Returning database in default state
 *
 * @param object $capsule WHMCS\Database\Capsule instance
 * @return void
 */
function resetDatabaseTable($capsule)
{
    dropTable($capsule);
    createTable($capsule);
}

/**
 * Dropping single table 
 *
 * @param object $capsule WHMCS\Database\Capsule instance
 * @return bool
 */
function dropTable($capsule)
{
    return $capsule::schema()
        ->dropIfExists('mod_mailerlite_settings');
}

/**
 * Creating single table
 *
 * @param object $capsule WHMCS\Database\Capsule instance
 * @return bool
 */
function createTable($capsule)
{
    return $capsule::schema()
        ->create(
            'mod_mailerlite_settings',
            function ($table) {
                /** @var \Illuminate\Database\Schema\Blueprint $table */
                $table->increments('id');
                $table->string('api_key');
                $table->integer('list_id');
                $table->tinyInteger('status');
                $table->timestamps();
            }
        );
    
}
