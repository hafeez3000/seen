<?php namespace app\components;

use \Yii;

trait PersonTrait
{
	public function getProfileUrl()
	{
		if (!empty($this->profile_path))
			return 'src="' . Yii::$app->params['themoviedb']['image_url'] . 'w45' . $this->profile_path . '"';
		else
			return 'data-src="holder.js/45x68/#eee:#555/text:' . $this->name . '"';
	}
}