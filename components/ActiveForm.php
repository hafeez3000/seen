<?php namespace app\components;

class ActiveForm extends \yii\widgets\ActiveForm
{
	public function init()
	{
		parent::init();

		if (isset($this->options['class']) && strpos('form-horizontal', $this->options['class']) !== false) {
			$this->fieldConfig['template'] = "{label}\n<div class=\"col-md-9\">\n{input}\n{hint}\n{error}\n</div>";

			$this->fieldConfig['labelOptions'] = [
				'class' => 'control-label col-md-3',
			];
		}
	}
}
