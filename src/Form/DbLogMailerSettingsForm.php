<?php

namespace Drupal\dblog_mailer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\RfcLogLevel;
/**
 * Class DbLogMailerSettingsForm
 * @package Drupal\dblog_mailer\Form
 */
class DbLogMailerSettingsForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dblog_mailer_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['dblog_mailer.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dblog_mailer.settings');

    // Build form elements.
    $form['settings'] = [
      '#type' => 'vertical_tabs',
      '#attributes' => ['class' => ['dblog-mailer']],
      '#attached' => [
        'library' => ['dblog_mailer/drupal.settings_form'],
      ],
    ];

    // General settings tab
    $form['general_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('General settings'),
      '#group' => 'settings',
    ];

    $form['general_settings']['enable'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable sending emails'),
      '#description' => $this->t('This setting can be forced to false in settings.php on non-production environments.'),
      '#default_value' => $config->get('enable'),
    ];

    $form['general_settings']['reply_to'] = [
      '#type' => 'email',
      '#title' => $this->t('Reply-to address to use for emails'),
      '#description' => $this->t(''),
      '#default_value' => $config->get('reply_to'),
    ];

    // Emails tab
    $form['emails'] = [
      '#type' => 'details',
      '#title' => $this->t('Email settings'),
      '#group' => 'settings',
    ];

    $form['emails']['emails_list'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Logs channels: emails configuration'),
      '#description' => $this->t('Enter one channel setting per line followed by the subject title and a list of recipients, ex:<br />channel;channel2|Email title|email1@domain.com;email2@domain.com'),
      '#default_value' => $config->get('emails_list'),
      '#rows' => 10,
		];

		$form['emails']['severity'] = [
      '#type' => 'select',
      '#title' => $this->t('Send this severity levels'),
			'#description' => $this->t('Choose which severity levels to send via email. If nothing choosed - all the logs are sent.'),
			'#options' => RfcLogLevel::getLevels(),
			'#multiple' => TRUE,			
			'#default_value' => $config->get('severity'),
		];

		$form['emails']['message_length'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Message length'),
			'#description' => $this->t('Log messages could be large. This allows to limit the length of the log message an to include link to the full message. If the message length is provided - the message itself is created as link to the system with full message content.'),
			'#default_value' => $config->get('message_length'),
    ];
    $form['emails']['default_row_limit'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Rows count'),
			'#description' => $this->t('Count of rows to send within one email message.'),
			'#default_value' => $config->get('default_row_limit')
    ];
    	
		
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Trim the text values.
    $form_state->setValue('emails_list', trim($form_state->getValue('emails_list')));

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('dblog_mailer.settings')
      ->set('emails_list', $form_state->getValue('emails_list'))
			->set('enable', $form_state->getValue('enable'))
			->set('severity', $form_state->getValue('severity'))			
      ->set('message_length', $form_state->getValue('message_length'))
      ->set('default_row_limit', $form_state->getValue('default_row_limit'))      
      ->set('reply_to', $form_state->getValue('reply_to'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
