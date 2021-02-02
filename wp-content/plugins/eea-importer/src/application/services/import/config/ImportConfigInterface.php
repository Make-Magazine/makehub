<?php

namespace EventEspresso\AttendeeImporter\application\services\import\config;

use EventEspresso\AttendeeImporter\application\services\import\config\models\ImportModelConfigInterface;
use EventEspresso\core\services\collections\CollectionInterface;
use EventEspresso\core\services\options\JsonWpOptionSerializableInterface;

/**
 * Class ImportConfig
 *
 * Interface for describing import configurations.
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
interface ImportConfigInterface extends JsonWpOptionSerializableInterface
{
    /**
     * @since 1.0.0.p
     * @return CollectionInterface|ImportModelConfigInterface[]
     */
    public function getModelConfigs();
}
// End of file ImportConfig.php
// Location: EventEspresso\core\services\import/ImportConfig.php
