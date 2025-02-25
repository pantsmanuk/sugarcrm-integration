<?php
form_security_validate('plugin_SugarCRM_config_update');

$f_db_hostname = gpc_get_string('db_hostname');
$f_db_username = gpc_get_string('db_username');
$f_db_password = gpc_get_string('db_password');
$f_db_database = gpc_get_string('db_database');
$f_case_url = gpc_get_string('case_url');
$f_user_uuid = gpc_get_string('user_uuid');

form_security_purge('plugin_SugarCRM_config_update');

if (plugin_config_get('db_database') != $f_db_database) {
    plugin_config_set('db_database', $f_db_database);
}
if (plugin_config_get('db_hostname') != $f_db_hostname) {
    plugin_config_set('db_hostname', $f_db_hostname);
}
if (plugin_config_get('db_password') != $f_db_password) {
    plugin_config_set('db_password', $f_db_password);
}
if (plugin_config_get('db_username') != $f_db_username) {
    plugin_config_set('db_username', $f_db_username);
}

if (plugin_config_get('case_url') != $f_case_url) {
    plugin_config_set('case_url', $f_case_url);
}

if (plugin_config_get('user_uuid') != $f_user_uuid) {
    plugin_config_set('user_uuid', $f_user_uuid);
}

print_successful_redirect(plugin_page('config', true));
