# Uncached Blocks

A Drupal module that provides a way to disable caching for specific block types and individual block instances.

## Features

- **Disable caching by block type**: Select which block types should never be cached
- **Disable caching by block ID**: Select specific placed blocks to disable caching for individual instances
- **Dynamic block selection**: Easy-to-use configuration interface that lists all available blocks
- **Multi-select form fields**: Select multiple blocks or block types at once
- **Wildcard support**: Disable caching for all instances of a block type (e.g., all menu blocks)
- **Works with any block**: System blocks, custom blocks, Views blocks, etc.

## Installation

1. Clone or download this module to your Drupal `modules/custom/` directory
2. Enable the module: `drush en uncached_blocks` or via the admin UI
3. Configure at: `/admin/config/system/uncached-blocks`

## Configuration

1. Navigate to **Configuration > System > Uncached Blocks Configuration**
2. Choose one or both of these options:

### Option 1: Disable by Block Type
Select block types to disable caching for all instances of that type:
- Select "Main menu" to disable caching for the main menu block
- Select "System Menu Block (all)" to disable caching for all menu blocks
- Select "Views Block" instances to disable caching for specific views

### Option 2: Disable by Block ID
Select specific block instances to disable caching for only those blocks:
- This is useful when you only want to disable caching for a particular instance of a block
- All placed blocks are listed with their labels and block types
- You can select multiple blocks

3. Save the configuration

## How It Works

The module implements `hook_block_build_alter()` to intercept block rendering and:
1. Checks if the block type matches configured block types
2. Checks if the block ID matches configured block IDs
3. Sets `max-age` to 0 for matching blocks, preventing them from being cached

## Requirements

- Drupal 10 or 11
- PHP 8.1+

## Examples

**Scenario 1: Disable all menu blocks**
- Go to the "Block Types" section
- Select "System Menu Block (all)"

**Scenario 2: Disable caching for a specific block instance**
- Go to the "Specific Block Instances" section
- Select the particular block you want to disable caching for

**Scenario 3: Mixed configuration**
- Disable all Views blocks in the "Block Types" section
- Disable specific custom blocks in the "Specific Block Instances" section
