<?php

use yii\db\Migration;

class m150304_093541_drop_email_tables extends Migration
{
	public function up()
	{
		$this->dropTable('{{%user_email_group}}');
		$this->dropTable('{{%email_group}}');

		$this->dropForeignKey('email_respond_user_id', '{{%email}}');
		$this->dropForeignKey('email_assigned_user_id', '{{%email}}');

		$this->dropIndex('respond_user_id', '{{%email}}');
		$this->dropIndex('assigned_user_id', '{{%email}}');
		$this->dropIndex('from_email', '{{%email}}');

		$this->dropColumn('{{%email}}', 'respond_user_id');
		$this->dropColumn('{{%email}}', 'respond_at');
		$this->dropColumn('{{%email}}', 'assigned_user_id');

		$this->dropIndex('to_email', '{{%email_to}}');

		$this->dropColumn('{{%email}}', 'success');

		$this->dropTable('{{%email_to}}');
		$this->dropTable('{{%email_attachment}}');
		$this->dropTable('{{%email}}');
	}

	public function down()
	{
		echo "m150304_093541_drop_email_tables cannot be reverted.\n";

		return false;
	}
}
