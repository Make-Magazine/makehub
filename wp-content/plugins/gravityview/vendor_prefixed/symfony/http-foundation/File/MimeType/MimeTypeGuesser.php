<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by gravityview on 23-February-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Symfony\Component\HttpFoundation\File\MimeType;

use GravityKit\GravityView\Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use GravityKit\GravityView\Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 * A singleton mime type guesser.
 *
 * By default, all mime type guessers provided by the framework are installed
 * (if available on the current OS/PHP setup).
 *
 * You can register custom guessers by calling the register() method on the
 * singleton instance. Custom guessers are always called before any default ones.
 *
 *     $guesser = MimeTypeGuesser::getInstance();
 *     $guesser->register(new MyCustomMimeTypeGuesser());
 *
 * If you want to change the order of the default guessers, just re-register your
 * preferred one as a custom one. The last registered guesser is preferred over
 * previously registered ones.
 *
 * Re-registering a built-in guesser also allows you to configure it:
 *
 *     $guesser = MimeTypeGuesser::getInstance();
 *     $guesser->register(new FileinfoMimeTypeGuesser('/path/to/magic/file'));
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class MimeTypeGuesser implements MimeTypeGuesserInterface
{
    /**
     * The singleton instance.
     *
     * @var MimeTypeGuesser
     */
    private static $instance = null;

    /**
     * All registered MimeTypeGuesserInterface instances.
     *
     * @var array
     */
    protected $guessers = [];

    /**
     * Returns the singleton instance.
     *
     * @return self
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Resets the singleton instance.
     */
    public static function reset()
    {
        self::$instance = null;
    }

    /**
     * Registers all natively provided mime type guessers.
     */
    private function __construct()
    {
        $this->register(new FileBinaryMimeTypeGuesser());
        $this->register(new FileinfoMimeTypeGuesser());
    }

    /**
     * Registers a new mime type guesser.
     *
     * When guessing, this guesser is preferred over previously registered ones.
     */
    public function register(MimeTypeGuesserInterface $guesser)
    {
        array_unshift($this->guessers, $guesser);
    }

    /**
     * Tries to guess the mime type of the given file.
     *
     * The file is passed to each registered mime type guesser in reverse order
     * of their registration (last registered is queried first). Once a guesser
     * returns a value that is not NULL, this method terminates and returns the
     * value.
     *
     * @param string $path The path to the file
     *
     * @return string The mime type or NULL, if none could be guessed
     *
     * @throws \LogicException
     * @throws FileNotFoundException
     * @throws AccessDeniedException
     */
    public function guess($path)
    {
        if (!is_file($path)) {
            throw new FileNotFoundException($path);
        }

        if (!is_readable($path)) {
            throw new AccessDeniedException($path);
        }

        foreach ($this->guessers as $guesser) {
            if (null !== $mimeType = $guesser->guess($path)) {
                return $mimeType;
            }
        }

        if (2 === \count($this->guessers) && !FileBinaryMimeTypeGuesser::isSupported() && !FileinfoMimeTypeGuesser::isSupported()) {
            throw new \LogicException('Unable to guess the mime type as no guessers are available (Did you enable the php_fileinfo extension?).');
        }

        return null;
    }
}
