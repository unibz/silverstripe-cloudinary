<?php

/**
 * Silverstripe Cloudinary Module configuration file.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package Vendor\Module
 * @author Made Media <developers@mademedia.co.uk>
 * @copyright 2017 Made Media Ltd.
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/MadeHQ/silverstripe-cloudinary.git
 */

use MadeHQ\Cloudinary\Utils\ImageShortcodeProvider;
use SilverStripe\View\Parsers\ShortcodeParser;

ShortcodeParser::get('default')
    ->register('image', [ImageShortcodeProvider::class, 'handle_shortcode']);

ShortcodeParser::get('regenerator')
    ->register('image', [ImageShortcodeProvider::class, 'regenerate_shortcode']);