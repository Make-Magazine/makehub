<?php
/**
 * @license MIT
 *
 * Modified by gravityview on 23-February-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\ThirdParty\Illuminate\Support;

use GravityKit\GravityView\Foundation\ThirdParty\Illuminate\Contracts\Support\Htmlable;

class HtmlString implements Htmlable
{
    /**
     * The HTML string.
     *
     * @var string
     */
    protected $html;

    /**
     * Create a new HTML string instance.
     *
     * @param  string  $html
     * @return void
     */
    public function __construct($html)
    {
        $this->html = $html;
    }

    /**
     * Get the HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->html;
    }

    /**
     * Get the HTML string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toHtml();
    }
}
