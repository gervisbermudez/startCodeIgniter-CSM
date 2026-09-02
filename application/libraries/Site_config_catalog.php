<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Canonical metadata for site_config rows.
 * Values stay in the database; this catalog fills labels, descriptions, types
 * and default rows that older installs may be missing.
 */
class Site_config_catalog
{
    /**
     * @return array config_name => meta
     */
    public static function definitions()
    {
        $boolean = self::data_boolean();
        $text = self::data_input('text', 'text', 250, 0);
        $email = self::data_input('email', 'email', 150, 5);
        $longtext = self::data_input('text', 'text', 500, 0);
        $number = self::data_input('number', 'number', 10, 1);
        $ga = self::data_input('text', 'text', 40, 0);

        return array(
            'SITE_TITLE' => array(
                'config_label' => 'Site title',
                'config_description' => 'Public name of the site. Used in the browser tab, admin chrome and Open Graph.',
                'config_type' => 'general',
                'config_data' => self::data_input('text', 'text', 80, 1),
                'default_value' => 'Start CMS',
            ),
            'SITE_ADMIN_EMAIL' => array(
                'config_label' => 'Admin email',
                'config_description' => 'Contact address for site notices and RSS metadata.',
                'config_type' => 'general',
                'config_data' => $email,
                'default_value' => '',
            ),
            'SITE_TELEPHONE' => array(
                'config_label' => 'Telephone',
                'config_description' => 'Public phone number shown by themes that render contact details.',
                'config_type' => 'general',
                'config_data' => $text,
                'default_value' => '',
            ),
            'SITE_ADDRESS' => array(
                'config_label' => 'Address',
                'config_description' => 'Public postal address shown by themes that render contact details.',
                'config_type' => 'general',
                'config_data' => $longtext,
                'default_value' => '',
            ),
            'SITE_ADDRESS_LINK_MAP' => array(
                'config_label' => 'Map link',
                'config_description' => 'URL to a map of the public address (Google Maps or similar).',
                'config_type' => 'general',
                'config_data' => self::data_input('url', 'url', 500, 0),
                'default_value' => '',
            ),
            'SITE_TIME_ZONE' => array(
                'config_label' => 'Time zone',
                'config_description' => 'Offset used when formatting dates in the admin and public site.',
                'config_type' => 'general',
                'config_data' => $text,
                'default_value' => 'UTC+0',
            ),
            'SITE_DATE_FORMAT' => array(
                'config_label' => 'Date format',
                'config_description' => 'PHP date() pattern for calendar days (for example Y-m-d).',
                'config_type' => 'general',
                'config_data' => self::data_input('text', 'text', 20, 1),
                'default_value' => 'Y-m-d',
            ),
            'SITE_TIME_FORMAT' => array(
                'config_label' => 'Time format',
                'config_description' => 'PHP date() pattern for hours (for example H:i).',
                'config_type' => 'general',
                'config_data' => self::data_input('text', 'text', 20, 1),
                'default_value' => 'H:i',
            ),
            'SITE_LIST_MAX_ENTRY' => array(
                'config_label' => 'List page size',
                'config_description' => 'Default number of items per page in admin lists.',
                'config_type' => 'general',
                'config_data' => $number,
                'default_value' => '20',
            ),
            'SITE_LIST_PUBLIC' => array(
                'config_label' => 'Public list size',
                'config_description' => 'Default number of items per page on public listings.',
                'config_type' => 'general',
                'config_data' => $number,
                'default_value' => '10',
            ),
            'SITE_HOME_PAGE_ID' => array(
                'config_label' => 'Home page ID',
                'config_description' => 'page_id served at the site root. 0 uses the default home template.',
                'config_type' => 'general',
                'config_data' => $number,
                'default_value' => '0',
            ),
            'SITE_ERROR_404_PAGE_ID' => array(
                'config_label' => '404 page ID',
                'config_description' => 'page_id shown when a public URL is not found.',
                'config_type' => 'general',
                'config_data' => $number,
                'default_value' => '0',
            ),
            'SITE_ACTIVE_BLOGS' => array(
                'config_label' => 'Public blog',
                'config_description' => 'When Off, public blog routes and the RSS feed are disabled.',
                'config_type' => 'general',
                'config_data' => json_encode(array(
                    'type_value' => 'string',
                    'validate_as' => 'text',
                    'handle_as' => 'switch',
                    'perm_values' => array('Off', 'On'),
                    'true' => 'On',
                    'false' => 'Off',
                )),
                'default_value' => 'On',
            ),
            'SITE_DESCRIPTION' => array(
                'config_label' => 'Site description',
                'config_description' => 'Short summary for search results, RSS and social previews.',
                'config_type' => 'seo',
                'config_data' => self::data_input('text', 'text', 160, 0),
                'default_value' => '',
            ),
            'SITE_AUTHOR' => array(
                'config_label' => 'Site author',
                'config_description' => 'Author name used in metadata when a page does not set its own.',
                'config_type' => 'seo',
                'config_data' => $text,
                'default_value' => '',
            ),
            'SITE_LANGUAGE' => array(
                'config_label' => 'Site language',
                'config_description' => 'Primary language code of the public site (html lang and RSS).',
                'config_type' => 'seo',
                'config_data' => json_encode(array(
                    'type_value' => 'string',
                    'validate_as' => 'text',
                    'handle_as' => 'select',
                    'input_type' => 'select',
                    'perm_values' => array('en', 'esp'),
                )),
                'default_value' => 'en',
            ),
            'SITE_LINK_GITHUB' => array(
                'config_label' => 'GitHub URL',
                'config_description' => 'Public GitHub profile or repository linked from the theme footer.',
                'config_type' => 'seo',
                'config_data' => self::data_input('url', 'url', 250, 0),
                'default_value' => '',
            ),
            'SITE_LINK_TWITTER' => array(
                'config_label' => 'Twitter URL',
                'config_description' => 'Public Twitter/X profile linked from the theme footer.',
                'config_type' => 'seo',
                'config_data' => self::data_input('url', 'url', 250, 0),
                'default_value' => '',
            ),
            'SITE_LINK_INSTAGRAM' => array(
                'config_label' => 'Instagram URL',
                'config_description' => 'Public Instagram profile linked from the theme footer.',
                'config_type' => 'seo',
                'config_data' => self::data_input('url', 'url', 250, 0),
                'default_value' => '',
            ),
            'SITE_LINK_LINKEDIN' => array(
                'config_label' => 'LinkedIn URL',
                'config_description' => 'Public LinkedIn profile linked from the theme footer.',
                'config_type' => 'seo',
                'config_data' => self::data_input('url', 'url', 250, 0),
                'default_value' => '',
            ),
            'THEME_PATH' => array(
                'config_label' => 'Active theme',
                'config_description' => 'Folder name under themes/ used to render the public site.',
                'config_type' => 'theme',
                'config_data' => $text,
                'default_value' => 'awesomeTheme',
            ),
            'ANALYTICS_ACTIVE' => array(
                'config_label' => 'Google Analytics',
                'config_description' => 'Injects the Analytics snippet on public pages when On.',
                'config_type' => 'integrations',
                'config_data' => $boolean,
                'default_value' => 'No',
            ),
            'ANALYTICS_ID' => array(
                'config_label' => 'Analytics Measurement ID',
                'config_description' => 'GA4 Measurement ID (G-XXXXXXXX). Required when Analytics is on.',
                'config_type' => 'integrations',
                'config_data' => $ga,
                'default_value' => '',
            ),
            'ANALYTICS_CODE' => array(
                'config_label' => 'Analytics head code',
                'config_description' => 'Optional extra snippet printed in <head> (gtag or Tag Manager).',
                'config_type' => 'integrations',
                'config_data' => $longtext,
                'default_value' => '',
            ),
            'PIXEL_ACTIVE' => array(
                'config_label' => 'Facebook Pixel',
                'config_description' => 'Injects the Meta Pixel snippet on public pages when On.',
                'config_type' => 'integrations',
                'config_data' => $boolean,
                'default_value' => 'No',
            ),
            'PIXEL_CODE' => array(
                'config_label' => 'Pixel ID or snippet',
                'config_description' => 'Meta Pixel ID or the full head snippet. Required when Pixel is on.',
                'config_type' => 'integrations',
                'config_data' => $longtext,
                'default_value' => '',
            ),
            'SITEM_TRACK_VISITORS' => array(
                'config_label' => 'First-party visitor tracking',
                'config_description' => 'Records visits in this CMS (user_tracking). Independent from Google Analytics.',
                'config_type' => 'integrations',
                'config_data' => $boolean,
                'default_value' => 'No',
            ),
            'SYSTEM_LOGGER' => array(
                'config_label' => 'System activity log',
                'config_description' => 'Writes admin actions (create, update, delete) to the system log.',
                'config_type' => 'system',
                'config_data' => $boolean,
                'default_value' => 'Si',
            ),
            'SYSTEM_API_LOGGER' => array(
                'config_label' => 'API request log',
                'config_description' => 'Stores REST requests in api_logs. Useful for debugging integrations.',
                'config_type' => 'system',
                'config_data' => $boolean,
                'default_value' => 'No',
            ),
            'DEBUG_MODE' => array(
                'config_label' => 'Debug mode',
                'config_description' => 'Turns on extra diagnostics in the admin. Keep Off on production.',
                'config_type' => 'system',
                'config_data' => json_encode(array(
                    'type_value' => 'boolean',
                    'validate_as' => 'boolean',
                    'handle_as' => 'switch',
                    'perm_values' => array('0', '1'),
                    'true' => '1',
                    'false' => '0',
                )),
                'default_value' => '0',
            ),
            'AUTO_CLEANUP_ENABLED' => array(
                'config_label' => 'Automatic log cleanup',
                'config_description' => 'When On, cron (or a manual run) deletes logs older than the retention days.',
                'config_type' => 'system',
                'config_data' => $boolean,
                'default_value' => 'No',
            ),
            'LOGGER_RETENTION_DAYS' => array(
                'config_label' => 'System log retention (days)',
                'config_description' => 'System log rows older than this many days are removed when cleanup runs. 0 keeps all.',
                'config_type' => 'system',
                'config_data' => $number,
                'default_value' => '90',
            ),
            'API_LOGS_RETENTION_DAYS' => array(
                'config_label' => 'API log retention (days)',
                'config_description' => 'API log rows older than this many days are removed when cleanup runs. 0 keeps all.',
                'config_type' => 'system',
                'config_data' => $number,
                'default_value' => '30',
            ),
            'USER_TRACKING_RETENTION_DAYS' => array(
                'config_label' => 'Visitor tracking retention (days)',
                'config_description' => 'First-party visit rows older than this many days are removed when cleanup runs. 0 keeps all.',
                'config_type' => 'system',
                'config_data' => $number,
                'default_value' => '180',
            ),
            'AUTO_BACKUP_ENABLED' => array(
                'config_label' => 'Automatic database backups',
                'config_description' => 'Creates gzipped SQL backups on the configured schedule (cron / hook).',
                'config_type' => 'system',
                'config_data' => $boolean,
                'default_value' => 'No',
            ),
            'AUTO_BACKUP_FREQUENCY' => array(
                'config_label' => 'Backup frequency',
                'config_description' => 'How often automatic backups run when they are enabled.',
                'config_type' => 'system',
                'config_data' => json_encode(array(
                    'type_value' => 'string',
                    'validate_as' => 'text',
                    'handle_as' => 'select',
                    'perm_values' => array(
                        'hourly' => 'Hourly',
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                    ),
                )),
                'default_value' => 'daily',
            ),
            'AUTO_BACKUP_RETENTION' => array(
                'config_label' => 'Backups to keep',
                'config_description' => 'Oldest automatic backups are deleted once this count is exceeded.',
                'config_type' => 'system',
                'config_data' => $number,
                'default_value' => '7',
            ),
            'AUTO_BACKUP_TIME' => array(
                'config_label' => 'Backup time (24h)',
                'config_description' => 'Preferred time of day for daily/weekly/monthly automatic backups (HH:MM).',
                'config_type' => 'system',
                'config_data' => self::data_input('time', 'text', 5, 0),
                'default_value' => '03:00',
            ),
            'LAST_AUTO_BACKUP' => array(
                'config_label' => 'Last automatic backup',
                'config_description' => 'Timestamp of the last successful automatic backup. Updated by the system.',
                'config_type' => 'internal',
                'config_data' => $text,
                'default_value' => '',
                'readonly' => 1,
            ),
            'LAST_UPDATE_FILEMANAGER' => array(
                'config_label' => 'Last files scan',
                'config_description' => 'Timestamp of the last files-module index update. Updated by the system.',
                'config_type' => 'internal',
                'config_data' => $text,
                'default_value' => '',
                'readonly' => 1,
            ),
            'LAST_CLEANUP_RUN' => array(
                'config_label' => 'Last log cleanup',
                'config_description' => 'Timestamp of the last log cleanup. Updated by the system.',
                'config_type' => 'internal',
                'config_data' => $text,
                'default_value' => '',
                'readonly' => 1,
            ),
            'UPDATER_LAST_CHECK_UPDATE' => array(
                'config_label' => 'Last update check',
                'config_description' => 'When the CMS last contacted GitHub for a newer package.',
                'config_type' => 'internal',
                'config_data' => $text,
                'default_value' => '',
                'readonly' => 1,
            ),
            'UPDATER_LAST_CHECK_DATA' => array(
                'config_label' => 'Update check payload',
                'config_description' => 'Cached JSON from the last remote version check. Not edited by hand.',
                'config_type' => 'internal',
                'config_data' => $longtext,
                'default_value' => '',
                'readonly' => 1,
            ),
            'UPDATER_MANUAL_CHECK' => array(
                'config_label' => 'Allow manual update check',
                'config_description' => 'Lets an administrator check GitHub for a newer CMS package from Settings.',
                'config_type' => 'updater',
                'config_data' => $boolean,
                'default_value' => 'Si',
            ),
        );
    }

    /**
     * Encode nested config_data for POST (string, array or object).
     *
     * @param mixed $raw
     * @return string
     */
    public static function encode_config_data($raw)
    {
        if ($raw === null || $raw === false || $raw === '') {
            return json_encode(new stdClass());
        }
        if (is_string($raw)) {
            json_decode($raw);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $raw;
            }
            return json_encode($raw);
        }
        if (is_array($raw) || is_object($raw)) {
            $encoded = json_encode($raw);
            return ($encoded === false) ? '{}' : $encoded;
        }
        return json_encode($raw);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function value_is_on($value)
    {
        if ($value === true || $value === 1) {
            return true;
        }
        $normalized = strtolower(trim((string) $value));
        return in_array($normalized, array('1', 'true', 'si', 'on', 'yes'), true);
    }

    /**
     * @return string
     */
    protected static function data_boolean()
    {
        return json_encode(array(
            'type_value' => 'boolean',
            'validate_as' => 'boolean',
            'handle_as' => 'switch',
            'perm_values' => array('No', 'Si'),
            'true' => 'Si',
            'false' => 'No',
        ));
    }

    /**
     * @param string $input_type
     * @param string $validate_as
     * @param int    $max
     * @param int    $min
     * @return string
     */
    protected static function data_input($input_type, $validate_as, $max, $min)
    {
        return json_encode(array(
            'type_value' => 'string',
            'validate_as' => $validate_as,
            'max_lenght' => (string) $max,
            'min_lenght' => (string) $min,
            'handle_as' => 'input',
            'input_type' => $input_type,
            'perm_values' => null,
        ));
    }
}
