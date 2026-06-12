<?php

declare(strict_types=1);

namespace Drupal\uncached_blocks\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\block\Entity\Block;
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
    $uncached_block_types = $config->get('uncached_block_types') ?? [];
    $uncached_block_ids = $config->get('uncached_block_ids') ?? [];

    // Get all available block definitions for types
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

    // Get all placed block instances
    $block_entities = Block::loadMultiple();
    $block_id_options = [];
    foreach ($block_entities as $block_entity) {
      $plugin = $block_entity->getPlugin();
      $label = $block_entity->label() ?? $block_entity->id();
      $plugin_label = $block_definitions[$plugin->getPluginId()]['admin_label'] ?? $plugin->getPluginId();
      $block_id_options[$block_entity->id()] = sprintf('%s (%s)', $label, $plugin_label);
    }
    asort($block_id_options);

    // Block types section
    $form['block_types_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Block Types'),
      '#description' => $this->t('Select block types to disable caching for all instances of that type.'),
      '#open' => TRUE,
    ];

    $form['block_types_section']['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Select which block types should never be cached. You can select individual blocks or wildcard options (marked with "all") to disable caching for all blocks of that type.'),
    ];

    $form['block_types_section']['uncached_block_types'] = [
      '#type' => 'select',
      '#title' => $this->t('Uncached block types'),
      '#description' => $this->t('Hold Ctrl (Cmd on Mac) to select multiple blocks.'),
      '#options' => $block_options,
      '#default_value' => $uncached_block_types,
      '#multiple' => TRUE,
      '#size' => 15,
    ];

    // Block instances section
    $form['block_ids_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Specific Block Instances'),
      '#description' => $this->t('Select specific placed blocks to disable caching for individual instances.'),
      '#open' => TRUE,
    ];

    if (empty($block_id_options)) {
      $form['block_ids_section']['no_blocks'] = [
        '#type' => 'item',
        '#markup' => $this->t('No placed blocks found. Place some blocks first in Layout Builder or the Block UI.'),
      ];
    } else {
      $form['block_ids_section']['uncached_block_ids'] = [
        '#type' => 'select',
        '#title' => $this->t('Uncached block instances'),
        '#description' => $this->t('Select specific block instances. Hold Ctrl (Cmd on Mac) to select multiple blocks.'),
        '#options' => $block_id_options,
        '#default_value' => $uncached_block_ids,
        '#multiple' => TRUE,
        '#size' => 15,
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $block_types = $form_state->getValue('uncached_block_types') ?? [];
    $block_ids = $form_state->getValue('uncached_block_ids') ?? [];

    $this->config('uncached_blocks.settings')
      ->set('uncached_block_types', array_values($block_types))
      ->set('uncached_block_ids', array_values($block_ids))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
