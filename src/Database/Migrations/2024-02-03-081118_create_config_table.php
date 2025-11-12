<?php

namespace simba\api\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Class CreateConfigTable.
 */
class CreateConfigTable extends Migration
{
    public function up(): void
    {
        // TODO: menu item
        $this->forge->addField([
            'id'         => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'group'      => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'key'        => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'value'      => ['type' => 'text', 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('konfigurasi', true);
    }

    //--------------------------------------------------------------------

    public function down(): void
    {
        $this->forge->dropTable('konfigurasi');
    }
}
