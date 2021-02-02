<?php

namespace EventEspresso\AttendeeImporter\application\services\import\config\models;

use EE_Model_Field_Base;
use EEM_Attendee;
use EEM_Base;
use EventEspresso\AttendeeImporter\application\services\import\config\ImportModelConfigInterface;
use EventEspresso\AttendeeImporter\application\services\import\mapping\ImportMappingCollection;

/**
 * Class ImportAnswerConfig
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class ImportAnswerConfig extends ImportModelConfigBase
{


    /**
     * Gets the model this configuration is for
     * @since 1.0.0.p
     * @return EEM_Base
     */
    public function getModel()
    {
        return EEM_Answer::instance();
    }


    /**
     * Gets the names of the fields on this model that are mapped.
     * @since 1.0.0.p
     * @return string[]
     */
    public function fieldNamesMapped()
    {
        return [
            'QST_ID',
            'ANS_ID',
            'ANS_value'
        ];
    }
}
// End of file ImportAnswerConfig.php
// Location: EventEspresso\AttendeeImporter\application\services\import\config\models/ImportAnswerConfig.php
