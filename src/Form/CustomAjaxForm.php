<?php
/**
 * @file
 * Contains Drupal\d8_activity_list\Form\CustomAjaxForm.
 */

namespace Drupal\d8_activity_list\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\Entity;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\State\State;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implements a CustomAjaxForm form.
 */
class CustomAjaxForm extends FormBase {

  protected $state;

  protected $dateFormatter;

  function __construct(State $state, DateFormatter $dateFormatter) {
    $this->state = $state;
    $this->dateFormatter = $dateFormatter;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('state'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_ajax_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = array();

    $last_submmision = $this->state->get('custom_ajax_form_last_submission');
    if ($last_submmision) {
      $form['last_submission'] = array(
        '#markup' => 'Last Form submission was done at ' . $this->dateFormatter->format($last_submmision),
      );
    }


    // U.G/P.G/Other
    $form['qualification'] = [
      '#type' => 'select',
      '#title' => $this->t('Qualification'),
      '#options' => [
        '1' => $this->t('U.G'),
        '2' => $this->t('P.G'),
        '3' => $this->t('Other'),
      ],
    ];

    $form['others_specify'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Others, please specify'),
      '#states' => array(
        // Only show this field when the value of type is other.
        'visible' => array(
          ':input[name="qualification"]' => array('value' => '3'),
        ),
      ),
    );

    $form['country'] = array(
      '#type' => 'select',
      '#title' => $this->t('Country'),
      '#options' => [
        'india' => $this->t('India'),
        'uk' => $this->t('UK'),
      ],
      '#ajax' => array(
        'event' => 'change',
        'wrapper' => 'country-wrapper',
        'callback' => array(get_class($this), 'country_ajax_callback'),
      ),
     );

    $form['country_wrapper'] = array('#prefix' => '<div class="country-wrapper">', '#suffix' => '</div>');

    $states_options = array(
      'RJ' => $this->t("Rajasthan"),
      'BR' => $this->t("Bihar")
    );

    $form['country_wrapper']['states'] = array(
      '#type' => 'select',
      '#title' => t('States'),
      '#options' => $states_options,
    );

     $form['submit'] = array(
       '#type' => 'submit',
       '#value' => $this->t('Submit')
     );

    return $form;
  }

  public static function country_ajax_callback(array $form, FormStateInterface $form_state) {
    $ajax_response = new AjaxResponse();
    // Get the value of the 'country' field.
    $country = $form_state->getValue('country');
    $uk_states = array(
      'mn' => t("Manchester"),
      'np' => t("NewPort"),
      'ox' => t("Oxford"),
      'rp' => t('Ripon'),
    );
    $indian_states = array(
      'RJ' => t("Rajasthan"),
      'BR' => t("Bihar")
    );

    switch ($country) {
      case 'india':

        // Set default options.
        $form['country_wrapper']['states'] = array(
          '#type' => 'select',
          '#title' => t('States'),
          '#options' => $indian_states,
        );
        break;
      case 'uk':
        $form['country_wrapper']['states'] = array(
          '#type' => 'select',
          '#title' => t('States'),
          '#options' => $uk_states,
        );
        break;
    }

    $ajax_response->addCommand(new ReplaceCommand(".country-wrapper", ($form['country_wrapper'])));

    return $ajax_response;

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->state->set('custom_ajax_form_last_submission', REQUEST_TIME);
  }

}
