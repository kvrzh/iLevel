<?php

use yii\db\Migration;

/**
 * Handles the creation of table `operation`.
 */
class m180110_141739_create_operation_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('operation', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'date' => $this->date(),
            'type' => "ENUM('profit','loss')",
            'value' => $this->decimal(19, 2),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('operation');
    }
}
