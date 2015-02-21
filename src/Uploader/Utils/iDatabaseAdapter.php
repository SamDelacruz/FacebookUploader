<?php

namespace Uploader\Utils;

/**
 * Interface iDatabaseAdapter for logging SOAP requests to a database
 * @package Uploader\Utils
 */
interface iDatabaseAdapter {
    public function logToDatabase($message, $ipAddress, $timestamp);
}