<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 12-December-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

require __DIR__.'/common.inc';

use GravityKit\GravityRevisions\Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use GravityKit\GravityRevisions\Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

$storage = new NativeSessionStorage();
$storage->setSaveHandler(new TestSessionHandler());
$flash = new FlashBag();
$storage->registerBag($flash);
$storage->start();

$flash->add('foo', 'bar');

print_r($flash->get('foo'));
echo empty($_SESSION) ? '$_SESSION is empty' : '$_SESSION is not empty';
echo "\n";

$storage->save();

echo empty($_SESSION) ? '$_SESSION is empty' : '$_SESSION is not empty';

ob_start(function ($buffer) { return str_replace(session_id(), 'random_session_id', $buffer); });
