<?php

namespace EventEspresso\AttendeeImporter\application\services\import\config;

use EventEspresso\AttendeeImporter\application\services\import\config\models\ImportModelConfigInterface;
use EventEspresso\core\exceptions\InvalidClassException;
use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\services\collections\CollectionInterface;
use stdClass;

/**
 * Class ImportConfigBase
 *
 * Description
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
abstract class ImportConfigBase implements ImportConfigInterface
{
    /**
     * @var CollectionInterface|ImportModelConfigInterface[]
     */
    protected $model_configs;

    /**
     * @var bool
     */
    protected $model_configs_initialized = false;

    public function __construct()
    {
    }

    /**
     * @since 1.0.0.p
     * @return CollectionInterface|ImportModelConfigInterface[]
     */
    public function getModelConfigs()
    {
        $this->checkModelConfigsInitialized();
        return $this->model_configs;
    }

    /**
     * @since 1.0.0.p
     * @throws InvalidEntityException
     */
    protected function checkModelConfigsInitialized()
    {
        if (! $this->model_configs_initialized) {
            $this->model_configs = $this->initializeModelConfigCollection();
            if (! $this->model_configs instanceof CollectionInterface) {
                throw new InvalidEntityException(
                    $this->model_configs,
                    'EventEspresso\AttendeeImporter\application\services\import\config\models\ImportModelConfigInterface'
                );
            }
            $this->model_configs_initialized = true;
        }
    }

    /**
     * Gets the collection of model configs for this import configuration.
     * @since 1.0.0.p
     * @return CollectionInterface|ImportModelConfigInterface[]
     */
    abstract protected function initializeModelConfigCollection();


    /**
     * Creates a simple PHP array or stdClass from this object's properties, which can be easily serialized using
     * wp_json_serialize().
     * @since 1.0.0.p
     * @return mixed
     */
    public function toJsonSerializableData()
    {
        $simple_obj = new stdClass();
        $simple_obj->json_model_configs = [];
        foreach ($this->getModelConfigs() as $model_config) {
            $simple_obj->json_model_configs[ $model_config->getModelName() ] = $model_config->toJsonSerializableData();
        }
        return $simple_obj;
    }

    /**
     * Initializes this object from data
     * @since 1.0.0.p
     * @param mixed $data
     * @return boolean success
     */
    public function fromJsonSerializedData($data)
    {
        if ($data instanceof stdClass
            && property_exists($data, 'json_model_configs')
            && $data->json_model_configs instanceof stdClass) {
            foreach ($data->json_model_configs as $json_key => $json_value) {
                    $obj = $this->getModelConfigs()->get($json_key);
                if ($obj instanceof ImportModelConfigInterface) {
                    $obj->fromJsonSerializedData($json_value);
                }
            }
            return true;
        }
        return false;
    }
}
// End of file ImportConfigBase.php
// Location: EventEspresso\core\services\import\config/ImportConfigBase.php
