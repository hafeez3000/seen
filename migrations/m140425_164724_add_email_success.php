<?php

use \yii\db\Migration;

class m140425_164724_add_email_success extends Migration
{
    public function up()
    {
    	$this->addColumn('{{%email}}', 'success', 'tinyint(1) DEFAULT "0" COMMENT "Success"');
    }

    public function down()
    {
        $this->dropColumn('{{%email}}', 'success');
    }
}
