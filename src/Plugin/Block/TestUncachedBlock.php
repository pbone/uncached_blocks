<?php

namespace Drupal\uncached_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[Block(
  id: 'test_uncached_block',
  admin_label: new TranslatableMarkup('Test Uncached Block'),
)]
class TestUncachedBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => 'Block rendered at: ' . date('Y-m-d H:i:s'),
      '#cache' => [
        'max-age' => 3600, // Cache for 1 hour by default
      ],
    ];
  }

}
