<?php
/**
 * WHMCS Sample Addon Module Test
 *
 * Sample PHPUnit test that asserts the fundamental requirements of a WHMCS
 * module, ensuring that the required config function is defined and contains
 * the required array keys.
 *
 * This is by no means intended to be a complete test, and does not exercise any
 * of the actual functionality of the functions within the module. We strongly
 * recommend you implement further tests as appropriate for your module use
 * case.
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

use PHPUnit\Framework\TestCase;
use WHMCS\Module\Addon\Mailerlite\Admin\Controller;
use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\Mailerlite\Exceptions\DbException;
use WHMCS\Module\Addon\Mailerlite\Helpers\ModuleHelperClass;

// class WHMCSModuleTest extends PHPUnit_Framework_TestCase
class MailerliteSettingTest extends TestCase
{
    /** @var string $moduleName */
    protected $moduleName = 'mailerlite';

    /**
     * Default vars set of data
     *
     * @var array
     */
    protected $vars;

    /**
     * DB connection
     *
     * @var object WHMCS\Database\Capsule instance
     */
    protected static $dbh;

    public function setUp(): void
    {
        self::$dbh = setUpDbTable();
        $this->vars = createDefaultVarsArray();
    }

    public function tearDown(): void
    {
        resetDatabaseTable(self::$dbh);
        self::$dbh = null;
        $this->vars = [];
    }

    /**
     * Asserts the setting has been added into db.
     */
    public function testActiveSettingCanBeAdded()
    {
        $_REQUEST = makeRequestData('synchronizedlist');
        $beforeInsert = Capsule::table('mod_mailerlite_settings')->where('status', 1)->get();
        $this->assertCount(0, $beforeInsert);

        $settingOne = (new Controller())->synchronizedlist($this->vars);

        $afterInsert = Capsule::table('mod_mailerlite_settings')->where('status', 1)->get();
        $this->assertCount(1, $afterInsert);

        $this->assertEquals($_REQUEST['mailerlite-token'], $afterInsert[0]->api_key);
    }

    /**
     * Deactivating setting
     *
     * @return void
     */
    public function testActiveSettingCanBeDeactivated()
    {
        $data = create([], 'active');

        $beforeInsert = Capsule::table('mod_mailerlite_settings')->where('status', 1)->get();
        $this->assertCount(1, $beforeInsert);

        $this->assertEquals($data['api_key'], $beforeInsert[0]->api_key);
        $setting = (new ModuleHelperClass())->disconnect($data['list_id']);

        $active = Capsule::table('mod_mailerlite_settings')->where('status', 1)->get();
        $this->assertCount(0, $active);

        $deativated = Capsule::table('mod_mailerlite_settings')->where('list_id', $data['list_id'])->get();
        $this->assertEquals(0, $deativated[0]->status);
    }

    /**
     * Asserts that Db Exception is thrown when adding second active settings, it can be only one active
     */
    public function testThereCanBeOnlyOneActiveSetting()
    {
        $data = create([], 'active');

        $beforeInsert = Capsule::table('mod_mailerlite_settings')->where('status', 1)->get();
        $this->assertCount(1, $beforeInsert);

        $this->assertEquals($data['api_key'], $beforeInsert[0]->api_key);

        $_REQUEST = makeRequestData('synchronizedlist');

        $this->expectException(DbException::class);
        $setting = (new Controller())->synchronizedlist($this->vars);
    }

    /**
     * Asserts that Db Exception is thrown when trying deactivation when there is no active deactivation
     */
    public function testDeactivationIsNotPossibleIfThereIsNoActiveSetting()
    {
        $data = create([], 'active');

        $getSetting = Capsule::table('mod_mailerlite_settings')->where('status', 1)->get();
        $this->assertCount(1, $getSetting);
        $this->assertEquals($data['list_id'], $getSetting[0]->list_id);

        $setting = (new ModuleHelperClass())->disconnect($data['list_id']);
        $getSettingAfter = Capsule::table('mod_mailerlite_settings')->where('status', 1)->get();
        $this->assertCount(0, $getSettingAfter);

        $this->expectException(DbException::class);
        (new ModuleHelperClass())->disconnect($data['list_id']);
    }
}
