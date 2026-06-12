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
   * Disables caching for specific block types based on configuration.
   */
  #[Hook('block_build_alter')]
  public function blockBuildAlter(array &$build, BlockPluginInterface $block): void {
    $config = \Drupal::config('uncached_blocks.settings');
    $uncached_blocks = $config->get('uncached_block_types') ?? [];

    if (empty($uncached_blocks)) {
      return;
    }

    // Check if the block's base ID matches any of the configured uncached blocks
    $block_base_id = $block->getBaseId();
    $block_plugin_id = $block->getPluginId();

    foreach ($uncached_blocks as $uncached_block) {
      // Support prefix matching for derived blocks (e.g., 'views_block:')
      if (str_starts_with($uncached_block, '*')) {
        $prefix = substr($uncached_block, 1);
        if (str_starts_with($block_plugin_id, $prefix)) {
          $build['#cache']['max-age'] = 0;
          return;
        }
      }
      // Exact match on base ID
      elseif ($block_base_id === $uncached_block || $block_plugin_id === $uncached_block) {
        $build['#cache']['max-age'] = 0;
        return;
      }
    }
  }

}
