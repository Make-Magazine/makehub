<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 12-December-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityRevisions\Foundation\ThirdParty\Illuminate\Support\Facades;

use GravityKit\GravityRevisions\Foundation\ThirdParty\Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;

/**
 * @method static int handle(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output = null)
 * @method static int call(string $command, array $parameters = [])
 * @method static int queue(string $command, array $parameters = [])
 * @method static array all()
 * @method static string output()
 *
 * @see \GravityKit\GravityRevisions\Foundation\ThirdParty\Illuminate\Contracts\Console\Kernel
 */
class Artisan extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ConsoleKernelContract::class;
    }
}
