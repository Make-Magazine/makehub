<?php
/**
 * @license MIT
 *
 * Modified by gravityview on 23-February-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

require __DIR__.'/common.inc';

use GravityKit\GravityView\Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

$storage = new NativeSessionStorage(['cookie_samesite' => 'lax']);
$storage->setSaveHandler(new TestSessionHandler());
$storage->start();

$_SESSION = ['foo' => 'bar'];

ob_start(function ($buffer) { return str_replace(session_id(), 'random_session_id', $buffer); });
