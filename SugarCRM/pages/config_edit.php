<?php

// MantisBT - A PHP based bugtracking system

// MantisBT is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 2 of the License, or
// (at your option) any later version.
//
// MantisBT is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Edit Core Formatting Configuration
 *
 * @copyright Copyright 2025 GGP Systems Limited
 *
 * @link https://www.mantisbt.org
 */
form_security_validate('plugin_SugarCRM_config_update');

auth_reathenticate();
access_ensure_global_level(config_get('manage_plugin_threshold'));

$f_db_hostname = gpc_get_string('db_hostname');
$f_db_username = gpc_get_string('db_username');
$f_db_password = gpc_get_string('db_password');
$f_db_database = gpc_get_string('db_database');
$f_case_url = gpc_get_string('case_url');
$f_user_uuid = gpc_get_string('user_uuid');

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

form_security_purge('plugin_SugarCRM_config_update');

print_header_redirect(plugin_page('config', true));
