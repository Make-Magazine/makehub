<?php
/**
 * @license MIT
 *
 * Modified by The GravityKit Team on 25-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\ThirdParty\Gettext\Extractors;

use GravityKit\GravityImport\Foundation\ThirdParty\Gettext\Translations;
use GravityKit\GravityImport\Foundation\ThirdParty\Gettext\Utils\DictionaryTrait;
use Symfony\Component\Yaml\Yaml as YamlParser;

/**
 * Class to get gettext strings from yaml.
 */
class YamlDictionary extends Extractor implements ExtractorInterface
{
    use DictionaryTrait;

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        $messages = YamlParser::parse($string);

        if (is_array($messages)) {
            static::fromArray($messages, $translations);
        }
    }
}
