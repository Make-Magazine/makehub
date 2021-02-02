<?php

namespace EventEspresso\AttendeeImporter\application\services\import;

use EventEspresso\AttendeeImporter\domain\services\commands\ImportBaseCommand;
use EventEspresso\AttendeeImporter\application\services\import\extractors\ImportExtractorBase;

/**
 * Class ImportTypeInterface
 *
 * Interface for classes that describe a particular job type. These are properties shared between a web UI and CLI
 * command.
 *
 * @package     Event Espresso
 * @author         Mike Nelson
 * @since         1.0.0.p
 *
 */
interface ImportTypeManagerInterface
{
    /**
     * Gets the name of this import type (translated).
     * @since 1.0.0.p
     * @return string
     */
    public function getName();

    /**
     * Gets the slug for this import type.
     * @since 1.0.0.p
     * @return string
     */
    public function getSlug();

    /**
     * Gets a string of HTML describing this import type.
     * @since 1.0.0.p
     * @return string
     */
    public function getDescription();

    /**
     * @since 1.0.0.p
     * @return ImportBaseCommand
     */
    public function getImportCommand($args);

    /**
     * @since 1.0.0.p
     * @return ImportExtractorBase
     */
    public function getExtractor();

    /**
     * @since 1.0.0.p
     * @return string
     */
    public function getPathToFiles();

    /**
     * @since 1.0.0.p
     * @return mixed
     */
    public function getUrlToFiles();

    /**
     * Gets the name of the capability required to use this type of importer.
     * @since 1.0.0.p
     * @return string
     */
    public function cap();
}
// End of file ImportTypeInterface.php
// Location: EventEspresso\core\services\import/ImportTypeInterface.php
