<?php namespace app\components;

use \Yii;

trait PersonTrait
{
	public function getProfileUrl()
	{
		if (!empty($this->profile_path))
			return Yii::$app->params['themoviedb']['image_url'] . 'w45' . $this->profile_path;
		else
			return 'http://placehold.it/45x68/eee/555&' . http_build_query(['text' => $this->name]);
	}
}