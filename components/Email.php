<?php namespace app\components;

use \Yii;

/**
 * The Email class send emails.
 */
class Email {
	/**
	 * Receiver
	 *
	 * @access public
	 * @var string
	 */
	public $to;

	/**
	 * Subject
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
	private $_pest;

	/**
	 * Mandrill API Key
	 *
	 * @access private
	 * @var string
	 */
	private $_key;

	/**
	 * Construct a new mail object
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		$this->_pest = new \PestJSON(Yii::$app->params['email']['mandrill']['baseUrl']);
		$this->_key = Yii::$app->params['email']['mandrill']['apikey'];
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
			$status = $this->_pest->post(
				'/messages/send-template.json',
				array(
					'key' => $this->_key,
					'template_name' => $template,
					'template_content' => $vars,
					'message' => array(
						'subject' => $this->subject,
						'from_email' => Yii::$app->params['email']['system'],
						'from_name' => Yii::$app->name,
						'to' => array(
							array(
								'email' => $this->to,
							)
						),
						'track_opens' => true,
						'track_clicks' => true,
						'url_strip_qs' => true,
						'preserve_recipients' => true,
						'global_merge_vars' => Yii::$app->params['email']['mandrill']['globalMergeVars'],
						'merge_vars' => $mergeVars,
						'tags' => $tags,
					),
				)
			);

			foreach ($status as $emailStatus) {
				switch ($emailStatus['status']) {
					case 'sent':
						if (!isset($success))
							$success = true;

						$success = $success and true;

						Yii::log(
							Yii::t(
								'Email',
								'Email `{subject}` sent to `{email}`.',
								array(
									'{subject}' => $this->subject,
									'{email}' => $this->to,
								)
							),
							'application\email'
						);
						break;
					case 'queued':
						Yii::log(
							Yii::t(
								'Email',
								'Queued email `{subject}` to `{email}`.',
								array(
									'{subject}' => $this->subject,
									'{email}' => $this->to,
								)
							),
							'application\email'
						);
						break;
					case 'rejected':
						Yii::log(
							Yii::t(
								'Email',
								'Rejected email `{subject}` to `{email}`.',
								array(
									'{subject}' => $this->subject,
									'{email}' => $this->to,
								)
							),
							'application\email'
						);
						break;
					case 'invalid':
						Yii::log(
							Yii::t(
								'Email',
								'Invalid email `{subject}` to `{email}`.',
								array(
									'{subject}' => $this->subject,
									'{email}' => $this->to,
								)
							),
							'application\email'
						);
						break;
					default:
						Yii::log(
							Yii::t(
								'Email',
								'Unknown email status `{status}` for email `{subject}` to `{email}`.',
								array(
									'{status}' => $emailStatus['status'],
									'{subject}' => $this->subject,
									'{email}' => $this->to,
								)
							),
							'application\email'
						);
						break;
				}
			}
		} catch (Exception $e) {
			Yii::log(
				Yii::t(
					'Email',
					'API Error while sending email `{subject}` to `{email}`: {message}',
					array(
						'{subject}' => $this->subject,
						'{email}' => $this->to,
						'{message}' => $e->getMessage(),
					)
				),
				'application\email'
			);

			$success = false;
		}

		if (!$success) {
			Yii::log(
				Yii::t(
					'Email',
					'Error while sending email `{subject}` to `{email}`',
					array(
						'{subject}' => $this->subject,
						'{email}' => $this->to,
					)
				),
				'application\email'
			);
		}

		return $success;
	}
}
