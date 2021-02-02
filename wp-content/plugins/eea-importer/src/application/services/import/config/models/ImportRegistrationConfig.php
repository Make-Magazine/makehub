<?php

namespace EventEspresso\AttendeeImporter\application\services\import\config\models;

use EE_Model_Field_Base;
use EEM_Base;
use EEM_Registration;

/**
 * Class ImportRegistrationConfig
 *
 * Description
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class ImportRegistrationConfig extends ImportModelConfigBase
{


    /**
     * Gets the model this configuration is for
     * @since 1.0.0.p
     * @return EEM_Base
     */
    public function getModel()
    {
        return EEM_Registration::instance();
    }

    /**
     * Gets the names of the fields on this model that are mapped.
     * @since 1.0.0.p
     * @return string[]
     */
    public function fieldNamesMapped()
    {
        return [];
    }
}
// End of file ImportRegistrationConfig.php
// Location: EventEspresso\AttendeeImporter\application\services\import\config\models/ImportRegistrationConfig.php
