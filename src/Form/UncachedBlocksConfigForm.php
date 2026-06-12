<?php

declare(strict_types=1);

namespace Drupal\uncached_blocks\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Block\BlockManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configuration form for Uncached Blocks module.
 */
class UncachedBlocksConfigForm extends ConfigFormBase {

  /**
   * The block manager.
   *
   * @var \Drupal\Core\Block\BlockManagerInterface
   */
  protected BlockManagerInterface $blockManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->blockManager = $container->get('plugin.manager.block');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['uncached_blocks.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'uncached_blocks_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('uncached_blocks.settings');
    $uncached_blocks = $config->get('uncached_block_types') ?? [];

    // Get all available block definitions
    $block_definitions = $this->blockManager->getDefinitions();
    $block_options = [];
    $base_ids = [];

    foreach ($block_definitions as $plugin_id => $definition) {
      $label = $definition['admin_label'] ?? $plugin_id;
      $block_options[$plugin_id] = $label;

      // Collect base IDs for wildcard matching options
      if (isset($definition['deriver'])) {
        $base_id = $definition['id'];
        if (!isset($base_ids[$base_id])) {
          $base_ids[$base_id] = $label . ' (all)';
        }
      }
    }

    // Add wildcard options
    foreach ($base_ids as $base_id => $label) {
      $block_options['*' . $base_id . ':'] = $label;
    }

    asort($block_options);

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Select which block types should never be cached. You can select individual blocks or wildcard options (marked with "all") to disable caching for all blocks of that type.'),
    ];

    $form['uncached_block_types'] = [
      '#type' => 'select',
      '#title' => $this->t('Uncached block types'),
      '#description' => $this->t('Hold Ctrl (Cmd on Mac) to select multiple blocks.'),
      '#options' => $block_options,
      '#default_value' => $uncached_blocks,
      '#multiple' => TRUE,
      '#size' => 15,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValue('uncached_block_types') ?? [];

    $this->config('uncached_blocks.settings')
      ->set('uncached_block_types', array_values($values))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
