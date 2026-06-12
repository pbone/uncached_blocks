# Uncached Blocks

A Drupal module that provides a way to disable caching for specific block types.

## Features

- **Dynamic block selection**: Select which block types should never be cached
- **Multi-select form field**: Easy-to-use configuration interface
- **Wildcard support**: Disable caching for all instances of a block type (e.g., all menu blocks)
- **Works with any block**: System blocks, custom blocks, Views blocks, etc.

## Installation

1. Clone or download this module to your Drupal `modules/custom/` directory
2. Enable the module: `drush en uncached_blocks` or via the admin UI
3. Configure at: `/admin/config/system/uncached-blocks`

## Configuration

1. Navigate to **Configuration > System > Uncached Blocks Configuration**
2. Select the block types you want to disable caching for
3. Save the configuration

### Examples

- Select "Main menu" to disable caching for the main menu block
- Select "System Menu Block (all)" to disable caching for all menu blocks
- Select "Views Block" instances to disable caching for specific views

## How It Works

The module implements `hook_block_build_alter()` to intercept block rendering and sets `max-age` to 0 for configured block types, preventing them from being cached.

## Requirements

- Drupal 10 or 11
- PHP 8.1+
