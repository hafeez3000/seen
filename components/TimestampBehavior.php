<?php namespace app\components;

class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
	protected function getValue($event)
	{
		return date('Y-m-d H:i:s');
	}
}
