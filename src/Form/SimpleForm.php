<?php
/**
 * @file
 * Contains Drupal\d8_activity_list\Form\SimpleForm.
 */

namespace Drupal\d8_activity_list\Form;

use Drupal\Core\Entity\Entity;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

/**
 * Implements a SimpleForm form.
 */
class SimpleForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'simple_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = array();

    if ($form_state->getValue('name')) {
      // Show the value the user entered.
     $form['name_value'] = array(
        '#markup' => 'Name value is ' . $form_state->getValue('name'),
      );
    } else {
      $form['name'] = array(
       '#type' => 'textfield',
       '#title' => $this->t('Name'),
       '#required' => TRUE
     );

     $form['submit'] = array(
      '#type' => 'submit',
       '#value' => $this->t('Submit')
     );
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $name_value = $form_state->getValue("name");
    if(strlen($name_value) < 5) {
      $form_state->setErrorByName("name", t("Name value must be atleast 5 chars long."));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild();
  }

}
