<?php

declare(strict_types=1);

namespace Drupal\uncached_blocks\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\block\Entity\Block;

/**
 * Configuration form for Uncached Blocks module.
 */
class UncachedBlocksConfigForm extends ConfigFormBase {

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
    $uncached_block_ids = $config->get('uncached_block_ids') ?? [];

    // Get all placed block instances
    $block_entities = Block::loadMultiple();
    $block_id_options = [];
    
    foreach ($block_entities as $block_entity) {
      $label = $block_entity->label() ?? $block_entity->id();
      $block_id = $block_entity->id();
      $block_id_options[$block_entity->id()] = sprintf('%s [%s]', $label, $block_id);
    }
    asort($block_id_options);

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Select specific placed blocks to disable caching for individual instances.'),
    ];

    if (empty($block_id_options)) {
      $form['no_blocks'] = [
        '#type' => 'item',
        '#markup' => $this->t('No placed blocks found. Place some blocks first in Layout Builder or the Block UI.'),
      ];
    } else {
      $form['uncached_block_ids'] = [
        '#type' => 'select',
        '#title' => $this->t('Blocks to exclude from cache'),
        '#description' => $this->t('Hold Ctrl (Cmd on Mac) to select multiple blocks.'),
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
    $block_ids = $form_state->getValue('uncached_block_ids') ?? [];

    $this->config('uncached_blocks.settings')
      ->set('uncached_block_ids', array_values($block_ids))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
