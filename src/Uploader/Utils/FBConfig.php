<?php

namespace Uploader\Utils;

/**
 * Utility class for storing App constants etc.
 * Class FBConfig
 * @package Uploader
 */
class FBConfig {
    const APP_ID = '1409910789303690';
    const APP_SECRET = '161ca6547ad7742302bba61a5c152f54';
    const HOST_ROOT = 'http://localhost:8888/';
    private static $permissions = array (
        'email',
        'user_location',
        'user_birthday',
        'publish_actions',
        'read_stream');
    
    static function getPermissions() {
        return self::$permissions;
    }
}