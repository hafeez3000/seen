<?php namespace app\commands;

use \Yii;
use \yii\console\Controller;

use \app\models\Language;

class LanguageController extends Controller
{
	public $defaultAction = 'create-missing';

	protected function flatten(array $array) {
		$return = array();
		array_walk_recursive($array, function($a) use (&$return) {
			$return[] = $a;
		});
		return $return;
	}

	protected function getLanguageFiles($path, $basePath = '')
	{
		return $this->flatten(array_filter(array_map(function($file) use($path, $basePath) {
			if (substr($file, 0, 1) == '.')
				return false;

			if (is_dir($path . '/'  . $file))
				return $this->getLanguageFiles($path . '/' . $file, $basePath . '/' . $file);
			else {
				if (empty($basePath))
					return $file;
				else
					return $basePath . '/' . $file;
			}
		}, scandir($path))));
	}

	public function actionCreateMissing()
	{
		$default = Yii::$app->params['lang']['default_iso'];

		$baseLanguage = Language::find()
			->where(['iso' => $default])
			->one();
		if ($baseLanguage === null) {
			echo "Base language not found in database!\n";
			return false;
		}

		$languageFiles = $this->getLanguageFiles(Yii::$app->basePath . '/messages/' . $default);

		$languages = Language::find()
			->where('[[iso]] != :iso')
			->params([
				':iso' => $baseLanguage->iso
			])
			->all();

		foreach ($languages as $language) {
			$languageBasePath = Yii::$app->basePath . '/messages/' . $language->iso . '/';

			foreach ($languageFiles as $languageFile) {
				$languageFilePath = $languageBasePath . $languageFile;

				if (!is_dir(dirname($languageFilePath))) {
					if (!mkdir(dirname($languageFilePath), 0777, true)) {
						echo "Could not create language base directory {$languageFilePath}!\n";
						continue;
					}
				}

				if (!file_exists($languageFilePath)) {
					file_put_contents($languageFilePath, "<?php\n\nreturn [];\n");
				}
			}
		}
	}
}
