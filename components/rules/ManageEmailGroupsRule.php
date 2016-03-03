<?php namespace app\components\rules;

use \yii\rbac\Rule;

class ManageEmailGroupsRule extends Rule
{
	public $name = 'manageEmailGroups';

	public function execute($user, $item, $params)
	{
		return false;
	}
}
