<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 12-December-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityRevisions\Foundation\ThirdParty\Gettext\Generators;

use GravityKit\GravityRevisions\Foundation\ThirdParty\Gettext\Translations;
use GravityKit\GravityRevisions\Foundation\ThirdParty\Gettext\Utils\MultidimensionalArrayTrait;

class PhpArray extends Generator implements GeneratorInterface
{
    use MultidimensionalArrayTrait;

    public static $options = [
        'includeHeaders' => true,
    ];

    /**
     * {@inheritdoc}
     */
    public static function toString(Translations $translations, array $options = [])
    {
        $array = static::generate($translations, $options);

        return '<?php return '.var_export($array, true).';';
    }

    /**
     * Generates an array with the translations.
     *
     * @param Translations $translations
     * @param array        $options
     *
     * @return array
     */
    public static function generate(Translations $translations, array $options = [])
    {
        $options += static::$options;

        return static::toArray($translations, $options['includeHeaders'], true);
    }
}
