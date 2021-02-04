<?php

namespace EventEspresso\AttendeeImporter\application\services\import\mapping\coercion;

use EventEspresso\core\domain\services\factories\FactoryInterface;
use EventEspresso\core\services\loaders\LoaderInterface;

/**
 * Class ImportFieldCoercionStrategyFactory
 *
 * Description
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class ImportFieldCoercionStrategyFactory
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }


    /**
     * @param string $coercion_strategy_name the name of the strategy, 'string' (default), 'boolean', 'bool',
     * 'state', or 'country' so far.
     * @return mixed
     */
    public function create($coercion_strategy_name)
    {
        switch (strtolower($coercion_strategy_name)) {
            case 'boolean':
            case 'bool':
                return $this->loader->getNew('EventEspresso\AttendeeImporter\application\services\import\mapping\coercion\ImportFieldCoerceBoolean');
            case 'state':
                return $this->loader->getNew('EventEspresso\AttendeeImporter\application\services\import\mapping\coercion\ImportFieldCoerceState');
            case 'country':
                return $this->loader->getNew('EventEspresso\AttendeeImporter\application\services\import\mapping\coercion\ImportFieldCoerceCountry');
            case 'string':
            default:
                return $this->loader->getNew('EventEspresso\AttendeeImporter\application\services\import\mapping\coercion\ImportFieldCoerceString');
        }
    }
}
// End of file ImportFieldCoercionStrategyFactory.php
// Location: EventEspresso\AttendeeImporter\application\services\import\mapping\coercion/ImportFieldCoercionStrategyFactory.php
