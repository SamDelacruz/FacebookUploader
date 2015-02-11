<?php

namespace Uploader\Utils;


interface iDatabaseAdapter {
    public function logToDatabase($message, $ipAddress, $timestamp);
}