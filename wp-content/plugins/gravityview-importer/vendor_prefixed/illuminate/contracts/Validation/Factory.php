<?php
/**
 * @license MIT
 *
 * Modified by The GravityKit Team on 25-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\ThirdParty\Illuminate\Contracts\Validation;

interface Factory
{
    /**
     * Create a new Validator instance.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return \GravityKit\GravityImport\Foundation\ThirdParty\Illuminate\Contracts\Validation\Validator
     */
    public function make(array $data, array $rules, array $messages = [], array $customAttributes = []);

    /**
     * Register a custom validator extension.
     *
     * @param  string  $rule
     * @param  \Closure|string  $extension
     * @param  string  $message
     * @return void
     */
    public function extend($rule, $extension, $message = null);

    /**
     * Register a custom implicit validator extension.
     *
     * @param  string   $rule
     * @param  \Closure|string  $extension
     * @param  string  $message
     * @return void
     */
    public function extendImplicit($rule, $extension, $message = null);

    /**
     * Register a custom implicit validator message replacer.
     *
     * @param  string   $rule
     * @param  \Closure|string  $replacer
     * @return void
     */
    public function replacer($rule, $replacer);
}
