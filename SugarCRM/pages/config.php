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
 * @copyright Copyright 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 * @copyright Copyright 2002  MantisBT Team - mantisbt-dev@lists.sourceforge.net
 *
 * @link http://www.mantisbt.org
 */
auth_reauthenticate();
access_ensure_global_level(config_get('manage_plugin_threshold'));

layout_page_header(lang_get('plugin_format_title'));

layout_page_begin('manage_overview_page.php');

print_manage_menu('manage_plugin_page.php');

$t_db_hostname = plugin_config_get('db_hostname');
$t_db_username = plugin_config_get('db_username');
$t_db_password = plugin_config_get('db_password');
$t_db_database = plugin_config_get('db_database');
$t_case_url = plugin_config_get('case_url');
$t_user_uuid = plugin_config_get('user_uuid');
?>

<div class="col-md-12 col-xs-12">
    <div class="space-10"></div>

    <div class="form-container">
        <form id="SugarCRM-config-form" action="<?php echo plugin_page('config_edit') ?>" method="post">
            <fieldset>
                <legend><span><?php echo plugin_lang_get('title').': '.plugin_lang_get('config') ?></span></legend>
                <?php echo form_security_field('plugin_SugarCRM_config_edit') ?>

                <div class="field-container">
                    <label><span><?php echo plugin_lang_get('db_hostname') ?></label>
                    <span class="small">
					<input type="text" name="db_hostname" value="<?php echo string_attribute($t_db_hostname) ?>"/>
				</span>
                    <span class="label-style"></span>
                </div>

                <div class="field-container">
                    <label><span><?php echo plugin_lang_get('db_username') ?></label>
                    <span class="small">
					<input type="text" name="db_username" value="<?php echo string_attribute($t_db_username) ?>"/>
				</span>
                    <span class="label-style"></span>
                </div>

                <div class="field-container">
                    <label><span><?php echo plugin_lang_get('db_password') ?></label>
                    <span class="small">
					<input type="text" name="db_password" value="<?php echo string_attribute($t_db_password) ?>"/>
				</span>
                    <span class="label-style"></span>
                </div>

                <div class="field-container">
                    <label><span><?php echo plugin_lang_get('db_database') ?></label>
                    <span class="small">
					<input type="text" name="db_database" value="<?php echo string_attribute($t_db_database) ?>"/>
				</span>
                    <span class="label-style"></span>
                </div>

                <div class="spacer">
                </div>

                <div class="field-container">
                    <label><span><?php echo plugin_lang_get('case_url') ?>
                <br/><span class="small"><?php echo plugin_lang_get('case_url_info') ?></span>
                    </label>
                    <span class="small">
					<input type="text" name="case_url" value="<?php echo string_attribute($t_case_url) ?>"/>
				</span>
                    <span class="label-style"></span>
                </div>

                <div class="spacer">
                </div>

                <div class="field-container">
                    <label><span><?php echo plugin_lang_get('user_uuid') ?>
                <br/><span class="small"><?php echo plugin_lang_get('user_uuid_info') ?></span>
                    </label>
                    <span class="small">
					<input type="text" name="user_uuid" value="<?php echo string_attribute($t_user_uuid) ?>"/>
				</span>
                    <span class="label-style"></span>
                </div>

                <span class="submit-button">
				<input type="submit" class="button" value="<?php echo lang_get('change_configuration') ?>"/>
			</span>
            </fieldset>
        </form>
    </div>
</div>

<?php
layout_page_end();
