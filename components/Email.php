<?php namespace app\components;

use \Yii;

/**
 * The Email class send emails.
 */
class Email {
	/**
	 * Receiver email.
	 *
	 * @access public
	 * @var string
	 */
	public $to;

	public $to_name = '';

	/**
	 * Subject.
	 *
	 * @access public
	 * @var string
	 */
	public $subject;

	/**
	 * Pest REST client object
	 *
	 * @access private
	 * @var Pest
	 */
	private $_mandrill;

	private $_fromName;

	private $_fromEmail;

	/**
	 * Construct a new mail object
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($fromName = null, $fromEmail = null)
	{
		$this->_mandrill = new \Mandrill(Yii::$app->params['email']['mandrill']['apikey']);

		$this->_fromName = ($fromName === null) ? Yii::$app->name : $fromName;
		$this->_fromEmail = ($fromEmail === null) ? Yii::$app->params['email']['system'] : $fromEmail;
	}

	/**
	 * Send the mail.
	 *
	 * @param string $template Name of the template
	 * @oaram array $vars Tempalte variables (array('name' => '', 'content' => ''))
	 * @param array $tags Tags for the email
	 *
	 * @access public
	 * @return boolean Whether the message was sent
	 */
	public function send($template, $vars, $tags = array())
	{
		$defaultMergeVars = array(
			array(
				'name' => 'subject',
				'content' => $this->subject,
			),
		);
		$mergeVars = array(
			array(
				'rcpt' => $this->to,
				'vars' => array_merge($defaultMergeVars, $vars),
			),

		);

		try {
			if (!YII_ENV_TEST) {
				$response = $this->_mandrill->messages->sendTemplate($template, $vars, [
					'subject' => $this->subject,
					'from_email' => $this->_fromEmail,
					'from_name' => $this->_fromName,
					'to' => [
						[
							'email' => $this->to,
							'name' => $this->to_name,
						]
					],
					'track_opens' => true,
					'track_clicks' => true,
					'url_strip_qs' => true,
					'preserve_recipients' => true,
					'global_merge_vars' => Yii::$app->params['email']['mandrill']['globalMergeVars'],
					'merge_vars' => $mergeVars,
					'tags' => $tags,
				], true);
			} else {
				return true;
			}
		} catch (\Mandrill_Error $e) {
			Yii::error("Could not send email: {$e->getMessage()}", 'application\email');
			return false;
		}

		return (isset($response[0]['status']) && $response[0]['status'] == 'sent');
	}

	public function sendRaw($text, $tags = array())
	{
		try {
			if (!YII_ENV_TEST) {
				$response = $this->_mandrill->messages->send([
					'text' => $text,
					'subject' => $this->subject,
					'from_email' => $this->_fromEmail,
					'from_name' => $this->_fromName,
					'to' => [
						[
							'email' => $this->to,
							'name' => $this->to_name,
						]
					],
					'track_opens' => false,
					'track_clicks' => false,
					'url_strip_qs' => true,
					'preserve_recipients' => true,
					'tags' => $tags,
				], true);
			} else {
				return true;
			}
		} catch (\Mandrill_Error $e) {
			Yii::error("Could not send email: {$e->getMessage()}", 'application\email');
			return false;
		}

		return (isset($response[0]['status']) && $response[0]['status'] == 'sent');
	}
}
