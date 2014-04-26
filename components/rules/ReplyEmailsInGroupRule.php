<?php namespace app\components\rules;

use \yii\rbac\Rule;
use \app\models\UserEmailGroup;

class ReplyEmailsInGroupRule extends Rule
{
	public $name = 'replyEmailsInGroup';

	public function execute($user, $item, $params)
	{
		if (!isset($params['groups']) || !is_array($params['groups']) || empty($params['groups']))
			return false;

		$groupIds = [];
		foreach ($params['groups'] as $group) {
			$groupIds[] = $group->id;
		}

		return UserEmailGroup::find()
			->where(['in', 'email_group_id', $groupIds])
			->andWhere(['user_id' => $user])
			->exists();
	}
}