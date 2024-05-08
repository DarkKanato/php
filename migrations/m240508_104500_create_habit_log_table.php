<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%habit_log}}`.
 */
class m240508_104500_create_habit_log_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%habit_log}}', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer()->notNull(),
            'log_date' => $this->date()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'FOREIGN KEY ([[task_id]]) REFERENCES {{%task}} ([[id]]) ON DELETE CASCADE',
        ]);

        // only one check-in per habit per day
        $this->createIndex('idx-habit_log-task_id-log_date', '{{%habit_log}}', ['task_id', 'log_date'], true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%habit_log}}');
    }
}
