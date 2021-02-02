<?php

namespace EventEspresso\AttendeeImporter\application\services\import\config\models;

use EE_Model_Field_Base;
use EEM_Attendee;
use EEM_Base;
use EEM_Payment;
use EventEspresso\AttendeeImporter\application\services\import\config\ImportModelConfigInterface;
use EventEspresso\AttendeeImporter\application\services\import\mapping\ImportMappingCollection;

/**
 * Class ImportPaymentConfig
 *
 * Configuration saying how to import a payment from source data.
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
class ImportPaymentConfig extends ImportModelConfigBase
{


    /**
     * Gets the model this configuration is for
     * @since 1.0.0.p
     * @return EEM_Base
     */
    public function getModel()
    {
        return EEM_Payment::instance();
    }


    /**
     * Gets the names of the fields on this model that are mapped.
     * @since 1.0.0.p
     * @return string[]
     */
    public function fieldNamesMapped()
    {
        return [
            'PAY_amount',
        ];
    }
}
// End of file ImportPaymentConfig.php
// Location: EventEspresso\AttendeeImporter\application\services\import\config\models/ImportPaymentConfig.php
