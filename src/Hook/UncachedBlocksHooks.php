<?php

declare(strict_types=1);

namespace Drupal\uncached_blocks\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Block\BlockPluginInterface;

/**
 * Hook implementations for uncached_blocks module.
 */
class UncachedBlocksHooks {

  /**
   * Implements hook_block_build_alter().
   *
   * Disables caching for specific block IDs based on configuration.
   */
  #[Hook('block_build_alter')]
  public function blockBuildAlter(array &$build, BlockPluginInterface $block): void {
    $config = \Drupal::config('uncached_blocks.settings');
    $uncached_block_ids = $config->get('uncached_block_ids') ?? [];

    if (empty($uncached_block_ids)) {
      return;
    }

    // Check specific block IDs
    if (isset($build['#block'])) {
      $block_entity = $build['#block'];
      if ($block_entity && in_array($block_entity->id(), $uncached_block_ids)) {
        $build['#cache']['max-age'] = 0;
      }
    }
  }

}
