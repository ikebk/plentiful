<?php
namespace Drupal\plentiful\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\plentiful\PlentifulApiClientInterface;
use Psr\Log\LoggerInterface;
use Drupal\Core\Access\AccessResult;

/**
* @Block(
* id = "plentiful_block",
* admin_label = @Translation("Plentiful"),
* category = @Translation("Custom")
* )
*/
class PlentifulBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The API client service.
   *
   * @var \Drupal\plentiful\PlentifulApiClientInterface
   */
  protected $apiClient;

  /**
   * Constructs a new CustomApiBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\plentiful\PlentifulApiClientInterface $api_client
   *   The API client service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PlentifulApiClientInterface $api_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->apiClient = $api_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plentiful.api_client')
    );
  } 

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $results = $this->apiClient
                    ->makeApiCall('api/users', ['page' => 1], $config['plt_items_per_page'])
                    ->getUsers();

    // Register api results event

    return [
      '#theme' => 'plentiful_list',
      '#results' => $results,
      '#labels' => [
        'label_email' => $config['plt_email_label'],
        'label_forename' => $config['plt_forename_label'],
        'label_surname' => $config['plt_surname_label'],
      ],
      '#attached' => [
        'library' => [
          'plentiful/plentiful-users',
        ],
      ],
    ];
  }

   /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();
    // $form['plentiful'] = [
    //   '#type' => 'details',
    //   '#title' => $this->t('Plentiful'),
    //   '#tree' => TRUE,
    //   '#group' => 'visibility_tabs',
    //   '#parents' => ['visibility_tabs'],
    // ];

    
    $form['plt_items_per_page'] = [
      '#type' => 'number',
      '#title' => $this->t('Items/page'),
      '#description' => $this->t('Number of items per page.'),
      '#default_value' => isset($config['plt_items_per_page']) ? $config['plt_items_per_page'] : -1,
      '#maxlength' => 3,
      // '#group' => 'plentiful',
      // '#parents' => ['plentiful'],
    ];
    
    $form['plt_email_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email label'),
      '#description' => $this->t('Email label.'),
      '#default_value' => isset($config['plt_email_label']) ? $config['plt_email_label'] : 'Email',
      // '#group' => 'verticaltabs',
    ];
    
    $form['plt_forename_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Forename label'),
      '#description' => $this->t('Forename label.'),
      '#default_value' => isset($config['plt_forename_label']) ? $config['plt_forename_label'] : 'Forename',
      // '#group' => 'verticaltabs',
    ];
    
    $form['plt_surname_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Surname label'),
      '#description' => $this->t('Surname label.'),
      '#default_value' => isset($config['plt_surname_label']) ? $config['plt_surname_label'] : 'Surname',
      // '#group' => 'verticaltabs',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();

    $this->configuration['plt_items_per_page'] = $values['plt_items_per_page'];
    $this->configuration['plt_email_label'] = $values['plt_email_label'];
    $this->configuration['plt_forename_label'] = $values['plt_forename_label'];
    $this->configuration['plt_surname_label'] = $values['plt_surname_label'];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }
}