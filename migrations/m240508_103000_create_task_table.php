<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%task}}`.
 *
 * Both one-off tasks (type = 0) and habits (type = 1) are stored here,
 * habit check-ins go to `habit_log`.
 */
class m240508_103000_create_task_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%task}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'type' => $this->smallInteger()->notNull()->defaultValue(0),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'priority' => $this->smallInteger()->notNull()->defaultValue(1),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'due_date' => $this->date(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            // sqlite can't add foreign keys with ALTER TABLE, so it has to be inside CREATE TABLE
            'FOREIGN KEY ([[user_id]]) REFERENCES {{%user}} ([[id]]) ON DELETE CASCADE',
        ]);

        $this->createIndex('idx-task-user_id-type', '{{%task}}', ['user_id', 'type']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%task}}');
    }
}
