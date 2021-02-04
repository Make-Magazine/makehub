<?php

namespace EventEspresso\AttendeeImporter\application\services\import;

use EventEspresso\core\libraries\form_sections\form_handlers\SequentialStepFormManager;
use EventEspressoBatchRequest\JobHandlerBaseClasses\JobHandler;

/**
 * Class ImportTypeUiInterface
 *
 * Interface for describing the web UI of an import. A CLI command would not use any of this.
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
interface ImportTypeUiManagerInterface
{

    /**
     * Gets the steps manager that corresponds to the import type.
     *
     * @param string $base_url base URL where these steps will be shown
     *                         (used for generating links to subsequent steps)
     * @return SequentialStepFormManager
     * @since 1.0.0.p
     */
    public function getStepManager($base_url = null);

    /**
     * Gets the batch system job handler that will take care of managing the import
     * (but if it's a CLI import, this doesn't apply)
     *
     * @return JobHandler
     * @since 1.0.0.p
     */
    public function getBatchJobHandler();

    /**
     * Gets the ImportType that this UI is for. That's stuff relating more to the actual import rather than UI.
     *
     * @since 1.0.0.p
     * @return ImportTypeManagerInterface
     */
    public function getImportType();

    /**
     * Gets URL of an image that describes the import type.
     *
     * @since 1.0.0.p
     * @return string
     */
    public function getImage();
}
// End of file ImportTypeUiInterface.php
// Location: EventEspresso\core\services\import/ImportTypeUiInterface.php
