<?php

namespace Drupal\d8_activity_list\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @file
 * Administration page callbacks for the Weather configurations.
 */

class WeatherConfigForm extends ConfigFormBase {
  public function getFormId() {
    return 'd8_activity_list_settings';
  }
  public function getEditableConfigNames() {
    return [
      'd8_activity_list.settings',
    ];

  }
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('d8_activity_list.settings');

    $form['appid'] = array(
      '#title' => $this->t('App ID'),
      '#type' => 'textfield',
      '#default_value' => $config->get('appid'),
    );

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->config('d8_activity_list.settings')
      ->set('appid', $form_state->getValue('appid'))
      ->save();

    // Set values in variables.
    parent::submitForm($form, $form_state);
  }
}