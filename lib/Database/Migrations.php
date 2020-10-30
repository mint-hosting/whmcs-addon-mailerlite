<?php 

namespace WHMCS\Module\Addon\Mailerlite\Database;

use WHMCS\Database\Capsule;
// use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\Module\Addon\Mailerlite\Exceptions\DbException;

/**
 * Migration class handles all DB migrations during
 * activation and deactivation processes
 */
class Migrations
{

    /**
     * Main method used to create tables
     *
     * @return array all created responses are put in this array
     */
    public function createTables()
    {
        $created = [];
        $created[] = $this->createMailerLiteSettingsTable();
        /**
         * Add your own method for additional tables
         * $created[] = $this->_yourMetohd();
         */

        return $created;
    }

    /**
     * Main method used for dropping tables from DB
     *
     * @return array all created responses are put in this array
     */
    public function dropTables()
    {
        $dropped = [];
        $dropped[] = $this->dropTable('mod_mailerlite_settings');
        // $dropped[] = $this->_dropTable('your_table');

        return $dropped;
    }

    /**
     * Dropping single table from DB
     *
     * @param string $tableName table name
     * @return array response
     */
    private function dropTable($tableName)
    {
        // Undo any database and schema modifications made by your module here
        try {
            Capsule::schema()
                ->dropIfExists($tableName);

            return [
                // Supported values here include: success, error or info
                'status' => 'success',
                'description' => $tableName . ' table is dropped successfully.'
            ];
        } catch (\DbException $e) {
            return [
                // Supported values here include: success, error or info
                'status' => 'error',
                'description' => 'Unable to drop ' . $tableName . 'table:' . $e->getMessage()
            ];
        }
    }

    /**
     * Creating main MailerLite settings table
     *
     * @return array response
     */
    private function createMailerLiteSettingsTable()
    {
        // Create custom tables and schema required by your module
        try {
            Capsule::schema()
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

            return [
                'status' => 'succes', 
                'description' => 'MailerLite Settings table is created successfully'
            ];
        } catch (DbException $e) {
            return [
                'status' => 'error', 
                'description' => 'MailerLite Settings table is not created: ' . $e->getMessage()
            ];
        }
    }
}
