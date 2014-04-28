<?php namespace app\components;

use \yii\log\Logger;
use \yii\log\Target;

class BugsnagLogger extends Target
{
	public function export()
	{
		$bugsnag = Yii::$app->bugsnag;

		$bugsnag->setBatchSending(true);

		foreach ($this->messages as $message) {
			$level = false;

			switch ($message[1]) {
				case Logger::LEVEL_WARNING:
					$level = 'warning';
					break;
				case Logger::LEVEL_ERROR:
					$level = 'error';
					break;
			}

			// Only log warnings and errors
			if ($level === false)
				continue;

			$name = $message[2];
			$content = $message[0];

			$bugsnag->notifyError($name, $content, null, $level);
		}
	}
}