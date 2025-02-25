<?php
auth_reauthenticate();
access_ensure_global_level(config_get('manage_plugin_threshold'));

html_page_top(lang_get('plugin_SugarCRM_title'));

print_manage_menu();

$t_db_hostname = plugin_config_get('db_hostname');
$t_db_username = plugin_config_get('db_username');
$t_db_password = plugin_config_get('db_password');
$t_db_database = plugin_config_get('db_database');
$t_case_url = plugin_config_get('case_url');
?>

    <div id="SugarCRM-config-div" class="form-container">
        <form id="SugarCRM-config-form" action="<?php echo plugin_page('config_edit') ?>" method="post">
            <fieldset>
                <legend><span><?php echo plugin_lang_get('title') . ': ' . plugin_lang_get('config') ?></span></legend>
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

<?php
html_page_bottom();
