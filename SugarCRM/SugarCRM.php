<?php

/**
 * SugarCRM integration plugin
 */
class SugarCRMPlugin extends MantisPlugin
{
    /**
     * A method that populates the plugin information and minimum requirements.
     */
    public function register(): void
    {
        $this->name = plugin_lang_get('title');
        $this->description = plugin_lang_get('description');
        $this->page = 'config';
        $this->version = '2.0.0';
        $this->requires = [
            'MantisCore' => '2.0.0',
        ];

        $this->author = 'Murray Crane';
        $this->contact = 'murray.crane@ggpsystems.co.uk';
        $this->url = 'https://www.mantisbt.org';
    }

    /**
     * Default plugin configuration.
     */
    public function config(): array
    {
        return [
            'db_hostname' => 'mysql.example.com',
            'db_username' => 'sugarcrm',
            'db_password' => 'strong_password',
            'db_database' => 'sugarcrm',
            'case_url' => 'https://sugarcrm.example.com/#Cases/',
            'user_uuid' => '01234567-0123-4567-890a-0123456789ab',
        ];
    }

    /**
     * Register events for plugin.
     */
    public function events(): array
    {
        return [
            'EVENT_SUGARCRM_CASE_URL' => EVENT_TYPE_OUTPUT,
            'EVENT_SUGARCRM_CASE_UPDATE' => EVENT_TYPE_EXECUTE,
            'EVENT_SUGARCRM_CASECSTM_UPDATE' => EVENT_TYPE_EXECUTE,
            'EVENT_SUGARCRM_COMMENTLOG_UPDATE' => EVENT_TYPE_EXECUTE,
        ];
    }

    /**
     * Register event hooks for plugin.
     */
    public function hooks(): array
    {
        return [
            'EVENT_SUGARCRM_CASE_URL' => 'getCaseUrl',
            'EVENT_SUGARCRM_CASE_UPDATE' => 'updateCase',
            'EVENT_SUGARCRM_CASECSTM_UPDATE' => 'updateCaseCstm',
            'EVENT_SUGARCRM_COMMENTLOG_UPDATE' => 'updateCommentlog',
        ];
    }

    /**
     * Get SugarCRM Case UUID using Case Number
     *
     * @param  int  $event  Whatever
     * @param  int  $chainedParam  SugarCRM Case Number
     * @return string URL to the SugarCRM Case number
     */
    public function getCaseUrl(int $event, int $chainedParam): string
    {
        if ($chainedParam !== 0 && $chainedParam !== '0000') {
            $caseUrl = plugin_config_get('case_url');
            $caseUuid = self::getCaseUuid($chainedParam);

            return "<a href=\"{$caseUrl}{$caseUuid}\">";
        }

        return '0000';
    }

    /**
     * Get SugarCRM Case UUID using Case Number
     *
     * @param  int|null  $caseNumber  SugarCRM Case number to retrieve UUID for
     * @return string UUID that equates to the SugarCRM Case number
     *
     * @throws Exception If database connection or query fails
     */
    public function getCaseUuid(?int $caseNumber = null): string
    {
        if ($caseNumber !== null) {
            try {
                $pdo = new PDO(
                    'mysql:host='.plugin_config_get('db_hostname').';dbname='.plugin_config_get('db_database'),
                    plugin_config_get('db_username'),
                    plugin_config_get('db_password')
                );
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $pdo->prepare('SELECT id FROM cases WHERE case_number = :case_number');
                $stmt->execute(['case_number' => $caseNumber]);
                $uuid = $stmt->fetchColumn();

                return $uuid ?: '';
            } catch (PDOException $e) {
                throw new Exception('Database error: '.$e->getMessage());
            }
        }

        return '';
    }

    /**
     * Update SugarCRM Case field/value
     *
     * @param  array  $params  Case number, field to be updated, and value to apply
     * @return bool Success/Failure
     *
     * @throws Exception If database connection or query fails
     */
    public function updateCase($event, $params): bool
    {
        $caseNumber = $params[0];
        $field = $params[1];
        $value = $params[2];

        if ($caseNumber !== null) {
            try {
                $pdo = new PDO(
                    'mysql:host='.plugin_config_get('db_hostname').';dbname='.plugin_config_get('db_database'),
                    plugin_config_get('db_username'),
                    plugin_config_get('db_password')
                );
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $query = "UPDATE `cases` SET `$field` = :value WHERE `case_number` = :case_number";
                $stmt = $pdo->prepare($query);
                $stmt->execute(['value' => $value, 'case_number' => $caseNumber]);

                return true;
            } catch (PDOException $e) {
                throw new Exception('Database error: '.$e->getMessage());
            }
        }

        return false;
    }

    /**
     * Update SugarCRM case_cstm field/value
     *
     * @param  array  $params  Case number, custom field to be updated, and value to apply
     * @return bool Success/Failure
     *
     * @throws Exception If database connection or query fails
     */
    public function updateCaseCstm($event, $params): bool
    {
        $caseNumber = $params[0];
        $field = $params[1];
        $value = $params[2];

        if ($caseNumber !== null) {
            $id = self::getCaseUuid($caseNumber);
            if ($id !== null) {
                try {
                    $pdo = new PDO(
                        'mysql:host='.plugin_config_get('db_hostname').';dbname='.plugin_config_get('db_database'),
                        plugin_config_get('db_username'),
                        plugin_config_get('db_password')
                    );
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $query = "UPDATE `cases_cstm` SET `$field` = :value WHERE `id_c` = :id";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute(['value' => $value, 'id' => $id]);

                    return true;
                } catch (PDOException $e) {
                    throw new Exception('Database error: '.$e->getMessage());
                }
            }
        }

        return false;
    }

    /**
     * Add SugarCRM commentlog field/value
     *
     * @param  array  $params  Case number, field to be updated, and value to apply
     * @return bool Success/Failure
     *
     * @throws Exception If database connection or query fails
     */
    public function updateCommentlog($event, $params): bool
    {
        $caseNumber = $params[0];
        $field = $params[1];
        $value = $params[2];
        $userUuid = plugin_config_get('user_uuid');
        $commentlogUuid = self::get_uuid_nc('commentlog');

        if ($caseNumber !== null) {
            try {
                $pdo = new PDO(
                    'mysql:host='.plugin_config_get('db_hostname').';dbname='.plugin_config_get('db_database'),
                    plugin_config_get('db_username'),
                    plugin_config_get('db_password')
                );
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $datetime = date('Y-m-d H:i:s');

                $query = 'INSERT INTO `commentlog` (`id`, `date_entered`, `date_modified`, `modified_user_id`, `created_by`, `deleted`, `entry`) 
                      VALUES (:id, :date_entered, :date_modified, :modified_user_id, :created_by, 0, :entry)';
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    'id' => $commentlogUuid,
                    'date_entered' => $datetime,
                    'date_modified' => $datetime,
                    'modified_user_id' => $userUuid,
                    'created_by' => $userUuid,
                    'entry' => "Reported In Revision: $field".PHP_EOL."Tested In Revision: $value",
                ]);

                $caseUuid = self::getCaseUuid($caseNumber);
                $commentlogRelUuid = self::get_uuid_nc('commentlog_rel');
                $relQuery = "INSERT INTO `commentlog_rel` (`id`, `record_id`, `commentlog_id`, `module`, `deleted`) 
                         VALUES (:id, :record_id, :commentlog_id, 'Cases', 0)";
                $relStmt = $pdo->prepare($relQuery);
                $relStmt->execute([
                    'id' => $commentlogRelUuid,
                    'record_id' => $caseUuid,
                    'commentlog_id' => $commentlogUuid,
                ]);

                return true;
            } catch (PDOException $e) {
                throw new Exception('Database error: '.$e->getMessage());
            }
        }

        return false;
    }

    /**
     * Make a new UUID without collision
     *
     * @param  string  $table  Database table to check for collision
     * @return string New UUID
     *
     * @throws Exception If database connection or query fails
     */
    public static function get_uuid_nc($table): string
    {
        try {
            $pdo = new PDO(
                'mysql:host='.plugin_config_get('db_hostname').';dbname='.plugin_config_get('db_database'),
                plugin_config_get('db_username'),
                plugin_config_get('db_password')
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            do {
                $uuid = self::v4();
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE id = :uuid");
                $stmt->execute(['uuid' => $uuid]);
                $count = $stmt->fetchColumn();
            } while ($count > 0);

            return $uuid;
        } catch (PDOException $e) {
            throw new Exception('Database error: '.$e->getMessage());
        }
    }

    /**
     * Generate a version 4 UUID.
     *
     * This function generates a random UUID according to RFC 4122.
     * A version 4 UUID is a universally unique identifier that is generated
     * using random numbers.
     *
     * @return string A randomly generated version 4 UUID.
     *
     * @throws \Random\RandomException
     */
    public static function v4(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            random_int(0, 0xFFFF), random_int(0, 0xFFFF),

            // 16 bits for "time_mid"
            random_int(0, 0xFFFF),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            random_int(0, 0x0FFF) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            random_int(0, 0x3FFF) | 0x8000,

            // 48 bits for "node"
            random_int(0, 0xFFFF), random_int(0, 0xFFFF), random_int(0, 0xFFFF)
        );
    }
}
