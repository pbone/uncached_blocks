# Uncached Blocks

A Drupal module that provides a way to disable caching for specific block instances.

## Features

- **Disable caching by block instance**: Select specific placed blocks to disable caching
- **Dynamic block selection**: Easy-to-use configuration interface that lists all placed blocks
- **Block ID display**: Shows block machine ID for easy identification
- **Multi-select form field**: Select multiple blocks at once
- **Works with any block**: System blocks, custom blocks, Views blocks, etc.

## Installation

1. Clone or download this module to your Drupal `modules/custom/` directory
2. Enable the module: `drush en uncached_blocks` or via the admin UI
3. Configure at: `/admin/config/system/uncached-blocks`

## Configuration

1. Navigate to **Configuration > System > Uncached Blocks Configuration**
2. Select the specific block instances you want to disable caching for
3. Save the configuration

### Display Format

Each block in the list is shown as: `Block Label [block-machine-id]`

For example:
- `Main Navigation [main-menu-block]`
- `Footer Copyright [footer-copyright-block]`
- `Recent Posts [recent-posts-view]`

## How It Works

The module implements `hook_block_build_alter()` to intercept block rendering and:
1. Checks if the block's entity ID matches the configured block IDs
2. Sets `max-age` to 0 for matching blocks, preventing them from being cached

## Requirements

- Drupal 10 or 11
- PHP 8.1+

## Use Cases

- Disable caching for a footer block that shows dynamic content
- Prevent caching for a block that displays user-specific information
- Disable caching for specific Views blocks while keeping others cached
- Fine-grained control over which placed block instances should always render fresh content
