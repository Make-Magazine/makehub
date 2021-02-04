<?php

namespace EventEspresso\AttendeeImporter\application\services\import;

/**
 * Class ImportTypeUiManagerBase
 *
 * For shared logic among all import type UI managers.
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
abstract class ImportTypeUiManagerBase implements ImportTypeUiManagerInterface
{
    /**
     * In order to use the slug as an identifier when putting this into a collection, we need a method ON THIS object
     * to get its slug. So this wraps just getting the import type and then the slug.
     * @since 1.0.0.p
     * @return string
     */
    public function getSlug()
    {
        return $this->getImportType()->getSlug();
    }
}
// End of file ImportTypeUiManagerBase.php
// Location: EventEspresso\AttendeeImporter\application\services\import/ImportTypeUiManagerBase.php
