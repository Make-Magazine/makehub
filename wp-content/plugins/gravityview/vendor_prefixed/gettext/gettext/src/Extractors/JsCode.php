<?php
/**
 * @license MIT
 *
 * Modified by gravityview on 23-February-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\ThirdParty\Gettext\Extractors;

use Exception;
use GravityKit\GravityView\Foundation\ThirdParty\Gettext\Translations;
use GravityKit\GravityView\Foundation\ThirdParty\Gettext\Utils\FunctionsScanner;

/**
 * Class to get gettext strings from javascript files.
 */
class JsCode extends Extractor implements ExtractorInterface, ExtractorMultiInterface
{
    public static $options = [
        'constants' => [],

        'functions' => [
            'gettext' => 'gettext',
            '__' => 'gettext',
            'ngettext' => 'ngettext',
            'n__' => 'ngettext',
            'pgettext' => 'pgettext',
            'p__' => 'pgettext',
            'dgettext' => 'dgettext',
            'd__' => 'dgettext',
            'dngettext' => 'dngettext',
            'dn__' => 'dngettext',
            'dpgettext' => 'dpgettext',
            'dp__' => 'dpgettext',
            'npgettext' => 'npgettext',
            'np__' => 'npgettext',
            'dnpgettext' => 'dnpgettext',
            'dnp__' => 'dnpgettext',
            'noop' => 'noop',
            'noop__' => 'noop',
        ],
    ];

    protected static $functionsScannerClass = 'GravityKit\GravityView\Foundation\ThirdParty\Gettext\Utils\JsFunctionsScanner';

    /**
     * @inheritdoc
     * @throws Exception
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        static::fromStringMultiple($string, [$translations], $options);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public static function fromStringMultiple($string, array $translations, array $options = [])
    {
        $options += static::$options;

        /** @var FunctionsScanner $functions */
        $functions = new static::$functionsScannerClass($string);
        $functions->saveGettextFunctions($translations, $options);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public static function fromFileMultiple($file, array $translations, array $options = [])
    {
        foreach (static::getFiles($file) as $file) {
            $options['file'] = $file;
            static::fromStringMultiple(static::readFile($file), $translations, $options);
        }
    }
}
