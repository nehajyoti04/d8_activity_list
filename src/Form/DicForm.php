<?php
/**
 * @file
 * Contains Drupal\d8_activity_list\Form\DicForm.
 */

namespace Drupal\d8_activity_list\Form;

use Drupal\Core\Entity\Entity;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\d8_activity_list\ConnectionService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implements a DIC form.
 */
class DicForm extends FormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;


  function __construct(ConnectionService $database) {
    $this->database = $database;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('d8_activity_list.database_storage')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dic_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = array();

    $query = $this->database->fetch();

    $form['first_name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
      '#required' => TRUE,
      '#default_value' => $query['first_name'],
    );

    $form['last_name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
      '#default_value' => $query['last_name']
    );

   $form['submit'] = array(
    '#type' => 'submit',
     '#value' => $this->t('Submit')
   );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->database->insert($form_state->getValue("first_name"), $form_state->getValue("last_name"));

  }

}
