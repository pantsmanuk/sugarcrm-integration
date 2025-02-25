<?php

/***
 * SugarCRM integration plugin
 */
class SugarCRMPlugin extends MantisPlugin
{
    /***
     * A method that populates the plugin information and minimum requirements.
     * @return void
     */
    public function register()
    {
        $this->name = plugin_lang_get('title');
        $this->description = plugin_lang_get('description');
        $this->page = 'config';
        $this->version = '2.0.0';
        $this->requires = array(
            'MantisCore' => '2.0.0',
        );

        $this->author = 'Murray Crane';
        $this->contact = 'murray.crane@ggpsystems.co.uk';
        $this->url = 'https://www.mantisbt.org';
    }

    /***
     * Default plugin configuration.
     * @return array
     */
    public function config()
    {
        return array(
            'db_hostname' => 'mysql.example.com',
            'db_username' => 'sugarcrm',
            'db_password' => 'strong_password',
            'db_database' => 'sugarcrm',
            'case_url' => 'https://sugarcrm.example.com/#Cases/',
            'user_uuid' => '01234567-0123-4567-890a-0123456789ab',
        );
    }

    /**
     * Register events for plugin.
     */
    public function events()
    {
        return array(
            'EVENT_SUGARCRM_CASE_URL' => EVENT_TYPE_OUTPUT,
            'EVENT_SUGARCRM_CASE_UPDATE' => EVENT_TYPE_EXECUTE,
            'EVENT_SUGARCRM_CASECSTM_UPDATE' => EVENT_TYPE_EXECUTE,
            'EVENT_SUGARCRM_COMMENTLOG_UPDATE' => EVENT_TYPE_EXECUTE,
        );
    }

    /**
     * Register event hooks for plugin.
     */
    public function hooks()
    {
        return array(
            'EVENT_SUGARCRM_CASE_URL' => 'getCaseUrl',
            'EVENT_SUGARCRM_CASE_UPDATE' => 'updateCase',
            'EVENT_SUGARCRM_CASECSTM_UPDATE' => 'updateCaseCstm',
            'EVENT_SUGARCRM_COMMENTLOG_UPDATE' => 'updateCommentlog',
        );
    }

    /***
     * Get SugarCRM Case UUID using Case Number
     *
     * @param int @p_event Whatever
     * @param int $p_chained_param SugarCRM Case Number
     *
     * @return string URL to the SugarCRM Case number
     */
    public function getCaseUrl($p_event, $p_chained_param)
    {
        if ($p_chained_param != null && $p_chained_param != "0000") {
            return '<a href="' . plugin_config_get('case_url') . self::getCaseUuid($p_chained_param) . '">';
        }
    }

    /***
     * Get SugarCRM Case UUID using Case Number
     *
     * @param int $p_casenumber SugarCRM Case number to retrieve UUID for
     *
     * @return string UUID that equates to the SugarCRM Case number
     */
    public function getCaseUuid($p_casenumber = null)
    {
        if ($p_casenumber != null) {
            $t_uuid = null;

            $t_errlevel = error_reporting(0);
            $t_mysqli = new mysqli(plugin_config_get('db_hostname'), plugin_config_get('db_username'), plugin_config_get('db_password'), plugin_config_get('db_database'));
            error_reporting($t_errlevel);

            if ($t_mysqli->connect_error) {
                die('Connect Error (' . $t_mysqli->connect_errno . ') ' . $t_mysqli->connect_error);
            }

            if ($t_stmt = $t_mysqli->prepare("SELECT id FROM cases WHERE case_number=?")) {
                $t_stmt->bind_param("s", $p_casenumber);
                $t_stmt->execute();
                $t_stmt->bind_result($t_uuid);
                $t_stmt->fetch();
                $t_stmt->close();
            }

            $t_mysqli->close();
            return $t_uuid;
        }

        return null;
    }

    /***
     * Update SugarCRM Case field/value
     *
     * @param array $p_params Case number, field to be updated and value to apply
     *
     * @return bool  Success/Failure
     */
    public function updateCase($p_event, $p_params)
    {
        $t_rtrn = false;
        $t_casenumber = $p_params[0];
        $t_field = $p_params[1];
        $t_value = $p_params[2];

        if ($t_casenumber != null) {
            $t_errlevel = error_reporting(0);
            $t_mysqli = new mysqli(plugin_config_get('db_hostname'), plugin_config_get('db_username'), plugin_config_get('db_password'), plugin_config_get('db_database'));
            error_reporting($t_errlevel);

            if ($t_mysqli->connect_errno) {
                die('Connect error (' . $t_mysqli->connect_errno . ') ' . $t_mysqli->connect_error);
            }

            $t_stmt = $t_mysqli->prepare("UPDATE `cases` SET `$t_field`=? WHERE `case_number`=?");
            $t_stmt->bind_param("si", $t_value, $t_casenumber);
            $t_stmt->execute();
            if ($t_stmt->errno == 0) {
                return true;
            }
            $t_stmt->close();
            $t_mysqli->close();
        }
        return $t_rtrn;
    }

    /***
     * Update SugarCRM case_cstm field/value
     *
     * @param array $p_params Case number, custom field to be updated and value to apply
     *
     * @return bool  Success/Failure
     */
    public function updateCaseCstm($p_event, $p_params)
    {
        $t_rtrn = false;
        $t_casenumber = $p_params[0];
        $t_field = $p_params[1];
        $t_value = $p_params[2];

        if ($t_casenumber != null) {
            $t_id = self::getCaseUuid($t_casenumber);
            if ($t_id != null) {
                $t_errlevel = error_reporting(0);
                $t_mysqli = new mysqli(plugin_config_get('db_hostname'), plugin_config_get('db_username'), plugin_config_get('db_password'), plugin_config_get('db_database'));
                error_reporting($t_errlevel);

                if ($t_mysqli->connect_errno) {
                    die('Connect error (' . $t_mysqli->connect_errno . ') ' . $t_mysqli->connect_error);
                }

                $t_stmt = $t_mysqli->prepare("UPDATE `cases_cstm` SET `$t_field`=? WHERE `id_c`=?");
                $t_stmt->bind_param("ss", $t_value, $t_id);
                $t_stmt->execute();
                if ($t_stmt->errno == 0) {
                    return true;
                }
                // $t_rtrn needs to be true if the UPDATE works...
                $t_stmt->close();
                $t_mysqli->close();
            }
        }
        return $t_rtrn;
    }

    /***
     * Add SugarCRM commentlog field/value
     *
     * @param array $p_params Case number, field to be updated and value to apply
     *
     * @return bool Success/Failure
     */
    public function updateCommentlog($p_event, $p_params)
    {
        $t_casenumber = $p_params[0];
        $t_field = $p_params[1];
        $t_value = $p_params[2];
        $t_user_uuid = plugin_config_get('user_uuid');
        $t_commentlog_uuid = self::get_uuid_nc('commentlog');

        if ($t_casenumber != null) {
            $t_errlevel = error_reporting(0);
            $t_mysqli = new mysqli(plugin_config_get('db_hostname'), plugin_config_get('db_username'), plugin_config_get('db_password'), plugin_config_get('db_database'));
            error_reporting($t_errlevel);

            if ($t_mysqli->connect_errno) {
                die('Connect error (' . $t_mysqli->connect_errno . ') ' . $t_mysqli->connect_error);
            }

            $t_datetime = date("Y-m-d H:i:s");

            $t_query = "INSERT INTO `commentlog` (`id`, `date_entered`, `date_modified`, `modified_user_id`, `created_by`, `deleted`, `entry`) VALUES ('$t_commentlog_uuid', '$t_datetime', '$t_datetime', '$t_user_uuid', '$t_user_uuid', 0, 'Reported In Revision: $t_field" . PHP_EOL . "Tested In Revision: $t_value')";
            $t_stmt = $t_mysqli->prepare($t_query);
            $t_stmt->execute();
            if ($t_stmt->errno == 0) {
                $t_stmt->close();

                $t_case_uuid = self::getCaseUuid($t_casenumber);
                $t_commentlog_rel_uuid = self::get_uuid_nc('commentlog_rel');
                $t_stmt = $t_mysqli->prepare("INSERT INTO `commentlog_rel` (`id`, `record_id`, `commentlog_id`, `module`, `deleted`) VALUES (?, ?, ?, 'Cases', 0)");
                $t_stmt->bind_param("sss", $t_commentlog_rel_uuid, $t_case_uuid, $t_commentlog_uuid);
                $t_stmt->execute();
                if ($t_stmt->errno == 0) {
                    return true;
                }
            }
            $t_stmt->close();
            $t_mysqli->close();
        }
        return false;
    }

    /***
     * Make a new UUID without collision
     *
     * @param string Database table to check for collision
     *
     * @return string New UUID
     */
    public static function get_uuid_nc($p_table)
    {
        $t_errlevel = error_reporting(0);
        $t_mysqli = new mysqli(plugin_config_get('db_hostname'), plugin_config_get('db_username'), plugin_config_get('db_password'), plugin_config_get('db_database'));
        error_reporting($t_errlevel);

        if ($t_mysqli->connect_errno) {
            die('Connect error (' . $t_mysqli->connect_errno . ') ' . $t_mysqli->connect_error);
        }

        $t_uuid = self::v4();
        do {
            if ($t_stmt = $t_mysqli->prepare("SELECT `id` FROM ? WHERE `id` = ?")) {
                $t_stmt->bind_param("ss", $p_table, $t_uuid);
                $t_stmt->execute();
                $t_stmt->bind_result($t_result);
                $t_stmt->fetch();
            }
            if ($t_result == NULL) break;
        } while (true);
        $t_mysqli->close();
        return $t_uuid;
    }

    public static function v4()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
