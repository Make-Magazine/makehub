<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 02-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

require __DIR__.'/common.inc';

use GravityKit\GravityEdit\Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

$storage = new NativeSessionStorage(['cookie_samesite' => 'lax']);
$storage->setSaveHandler(new TestSessionHandler());
$storage->start();

$_SESSION = ['foo' => 'bar'];

$storage->regenerate(true);

ob_start(function ($buffer) { return preg_replace('~_sf2_meta.*$~m', '', str_replace(session_id(), 'random_session_id', $buffer)); });
