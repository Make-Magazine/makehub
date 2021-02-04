<?php

namespace EventEspresso\AttendeeImporter\application\services\import\config\models;

use EE_Model_Field_Base;
use EEM_Attendee;
use EEM_Base;
use EventEspresso\AttendeeImporter\application\services\import\config\ImportModelConfigInterface;
use EventEspresso\AttendeeImporter\application\services\import\mapping\ImportMappingCollection;

/**
 * Class ImportAttendeeConfig
 *
 * Description
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class ImportAttendeeConfig extends ImportModelConfigBase
{


    /**
     * Gets the model this configuration is for
     * @since 1.0.0.p
     * @return EEM_Base
     */
    public function getModel()
    {
        return EEM_Attendee::instance();
    }


    /**
     * Gets the names of the fields on this model that are mapped.
     * @since 1.0.0.p
     * @return string[]
     */
    public function fieldNamesMapped()
    {
        return [
            'ATT_fname',
            'ATT_lname',
            'ATT_email',
            'ATT_address',
            'ATT_address2',
            'ATT_city',
            'STA_ID',
            'CNT_ISO',
            'ATT_zip',
            'ATT_phone',
        ];
    }
}
// End of file ImportAttendeeConfig.php
// Location: EventEspresso\AttendeeImporter\application\services\import\config\models/ImportAttendeeConfig.php
