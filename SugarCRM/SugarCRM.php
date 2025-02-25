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
        $this->version = '2.27.0';
        $this->requires = [
            'MantisCore' => '2.0.0',
        ];

        $this->author = 'Murray Crane';
        $this->contact = 'murray.crane@ggpsystems.co.uk';
        $this->url = 'https://www.github.com/pantsmanuk/sugarcrm-integration';
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
     * Register event hooks for plugin.
     */
    public function hooks(): array
    {
        return [
            'EVENT_DISPLAY_CUSTOM_FIELD' => 'getCaseUrl',
        ];
    }

    /**
     * Get SugarCRM Case UUID using Case Number
     * @TODO MBT custom field name, custom field default value and SugarCRM custom field name should come from config - MLC
     *
     * @param  int  $event  Whatever
     * @param  int  $chainedParam  SugarCRM Case Number
     * @return string URL to the SugarCRM Case number
     */
    public function getCaseUrl($event, $fieldId, $issue_id, $fieldValue): string
    {
        if ($fieldId == custom_field_get_id_from_name('Sugar Case Number')) {
            if (! empty($fieldValue) && ($fieldValue !== 0 || $fieldValue !== '0000')) {
                $caseUrl = plugin_config_get('case_url');
                $caseUuid = $this->getCaseUuid($fieldValue);

                $this->updateCaseCstm($event, [$caseUuid, 'scarab_c', $fieldValue]);

                return '<a href="'.htmlspecialchars($caseUrl.$caseUuid).'">'.htmlspecialchars($fieldValue).'</a>';
            }
        }

        return $fieldValue;
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
     * Update SugarCRM case_cstm field/value
     *
     * @param  array  $params  Case UUID, custom field to be updated, and value to apply
     * @return bool Success/Failure
     *
     * @throws Exception If database connection or query fails
     */
    public function updateCaseCstm($event, $params): bool
    {
        $caseUuid = $params[0];
        $field = $params[1];
        $value = $params[2];

        if ($caseUuid !== null) {
            try {
                $pdo = new PDO(
                    'mysql:host='.plugin_config_get('db_hostname').';dbname='.plugin_config_get('db_database'),
                    plugin_config_get('db_username'),
                    plugin_config_get('db_password')
                );
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $query = "UPDATE `cases_cstm` SET `$field` = :value WHERE `id_c` = :id";
                $stmt = $pdo->prepare($query);
                $stmt->execute(['value' => $value, 'id' => $caseUuid]);

                return true;
            } catch (PDOException $e) {
                throw new Exception('Database error: '.$e->getMessage());
            }
        }

        return false;
    }
}
