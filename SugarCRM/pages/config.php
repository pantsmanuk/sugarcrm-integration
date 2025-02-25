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
            <?php echo form_security_field('plugin_SugarCRM_config_edit') ?>

            <div class="widget-box widget-color-blue2">
                <div class="widget-header widget-header-small">
                    <h4 class="widget-title lighter">
                        <?php echo sprintf('%s: %s',
                        lang_get('plugin_SugarCRM_title'),
                        lang_get('plugin_SugarCRM_config')
                        ); ?>
                    </h4>
                </div>
            </div>

            <div class="widget-body">
                <div class="widget-main no-padding">
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed table-striped">
                            <tr>
                                <th class="category">
                                    <label for="db_hostname"><?php echo lang_get('plugin_SugarCRM_db_hostname'); ?></label>
                                </th>
                                <td>
                                    <input id="db_hostname" name="db_hostname" type="text" class="input-sm" size="50" maxlength="500" value="<?php echo string_attribute(plugin_config_get('db_hostname')) ?>"/>
                                </td>
                            </tr>

                            <tr>
                                <th class="category">
                                    <label for="db_username"><?php echo lang_get('plugin_SugarCRM_db_username'); ?></label>
                                </th>
                                <td>
                                    <input id="db_username" name="db_username" type="text" class="input-sm" size="50" maxlength="500" value="<?php echo string_attribute(plugin_config_get('db_username')) ?>"/>
                                </td>
                            </tr>

                            <tr>
                                <th class="category">
                                    <label for="db_password"><?php echo lang_get('plugin_SugarCRM_db_password'); ?></label>
                                </th>
                                <td>
                                    <input id="db_password" name="db_password" type="text" class="input-sm" size="50" maxlength="500" value="<?php echo string_attribute(plugin_config_get('db_password')) ?>"/>
                                </td>
                            </tr>

                            <tr>
                                <th class="category">
                                    <label for="db_database"><?php echo lang_get('plugin_SugarCRM_db_database'); ?></label>
                                </th>
                                <td>
                                    <input id="db_database" name="db_database" type="text" class="input-sm" size="50" maxlength="500" value="<?php echo string_attribute(plugin_config_get('db_database')) ?>"/>
                                </td>
                            </tr>

                            <tr>
                                <th class="category">
                                    <label for="case_url"><?php echo lang_get('plugin_SugarCRM_case_url'); ?></label>
                                    <br/>
                                    <span class="small">
                                        <?php echo lang_get('plugin_SugarCRM_case_url_info'); ?>
                                    </span>
                                </th>
                                <td>
                                    <input id="case_url" name="case_url" type="text" class="input-sm" size="50" maxlength="500" value="<?php echo string_attribute(plugin_config_get('case_url')) ?>"/>
                                </td>
                            </tr>

                            <div class="spacer">
                            </div>

                            <tr>
                                <th class="category">
                                    <label for="user_uuid"><?php echo lang_get('plugin_SugarCRM_user_uuid'); ?></label>
                                    <br/>
                                    <span class="small">
                                        <?php echo lang_get('plugin_SugarCRM_user_uuid_info'); ?>
                                    </span>
                                </th>
                                <td>
                                    <input id="user_uuid" name="user_uuid" type="text" class="input-sm" size="50" maxlength="500" value="<?php echo string_attribute(plugin_config_get('user_uuid')) ?>"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="widget-toolbox padding-8 clearfix">
                    <input type="submit" class="btn btn-primary btn-white btn-round" value="<?php echo lang_get('change_configuration') ?>"/>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
layout_page_end();
