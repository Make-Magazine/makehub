<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 02-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\ThirdParty\Gettext\Generators;

use GravityKit\GravityEdit\Foundation\ThirdParty\Gettext\Translations;
use GravityKit\GravityEdit\Foundation\ThirdParty\Gettext\Utils\DictionaryTrait;
use Symfony\Component\Yaml\Yaml as YamlDumper;

class YamlDictionary extends Generator implements GeneratorInterface
{
    use DictionaryTrait;

    public static $options = [
        'includeHeaders' => false,
        'indent' => 2,
        'inline' => 3,
    ];

    /**
     * {@inheritdoc}
     */
    public static function toString(Translations $translations, array $options = [])
    {
        $options += static::$options;

        return YamlDumper::dump(
            static::toArray($translations, $options['includeHeaders']),
            $options['inline'],
            $options['indent']
        );
    }
}
