<?php namespace app\components\rules;

use \yii\rbac\Rule;

class ViewEmailsInGroupRule extends Rule
{
	public $name = 'viewEmailsInGroup';

	public function execute($user, $item, $params)
	{
		return false;
	}
}
