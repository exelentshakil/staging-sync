<div class="header-section">
    <div class="logo">
        <img width="250" src="<?php echo SSYNC_ASSETS . '/images/ssync.png' ?>" alt="">
    </div>
</div>
<div class="settings-section">
    <div class="settings">
        <div id="errorsarea"></div>
        <?php echo wp_kses_post($msg); ?><br/>
        <form id="login_options_form" name="options_form" method="post"
              action="<?php echo esc_attr(filter_input(INPUT_SERVER, 'REQUEST_URI')); ?>">
            <?php wp_nonce_field('tss_options', 'tss_options'); ?>

            <h2 class="title"><?php _e('Welcome to Staging Sync!', 'tss'); ?></h2>
            <p class="description"><?php _e('Staging Sync is a free utility meant to help make it easier to keep your staging site in site with live data. Enjoy!', 'tss'); ?></p>
            <div class="status">
                <p>This site is the</p>
                <div class="site live-site <?php echo esc_attr(get_option('ssync_mode', 'live') ==
                'staging' ? '' : 'active-site'); ?>">
                    <label>
                        <div class="icon-live"><img src="<?php echo SSYNC_ASSETS . '/images/live.png' ?>" alt=""></div>
                        <p class="text"><?php _e('This is the site receiving live traffic currently', 'tss'); ?></p>
                        <input name="ssync_mode" type="radio" onclick="displayLive();"
                               value="live" <?php if (get_option('ssync_mode', 'live') ==
                            'live') echo 'checked="checked"'; ?>>
                    </label>
                </div>
                <div class="sep"> ></div>
                <div class="site staging-site <?php echo esc_attr(get_option('ssync_mode', 'live') ==
                'staging' ? 'active-site' : ''); ?>">
                    <label>
                        <div class="icon-live"><img src="<?php echo SSYNC_ASSETS . '/images/staging.png' ?>" alt="">
                        </div>
                        <p class="text"><?php _e('This is the site you are developing', 'tss'); ?></p>
                        <input name="ssync_mode" type="radio" onclick="displayStaging();"
                               value="staging" <?php if (get_option('ssync_mode', 'live') ==
                            'staging') echo 'checked="checked"'; ?>>
                    </label>
                </div>
            </div>
            <div class="form-field-group">
                <div class="form-field-radio">
                    <label for="ssync_mode"><?php _e('Mode', 'tss'); ?></label>
                    <?php _e('Live', 'tss'); ?>
                    <?php _e('Staging', 'tss'); ?>

                </div>
                <div class="form-field">
                    <label for="ssync_secret"><?php _e('Private Key', 'tss'); ?></label>
                    <input name="ssync_secret" id="ssync_secret" type="text"
                           value="<?php echo esc_attr(get_option('ssync_secret', '')); ?>" class="regular-text code">
                    <span class="field-info">
                            <?php _e('Your private key is a secret word or phrase that helps to associate and protect your staging site link', 'tss'); ?>
                        </span>
                </div>
            </div>
            <div class="form-field-group">
                <div class="form-field live <?php if (get_option('ssync_mode', 'live') ==
                    'staging') echo 'hidden'; ?>">
                    <label for="ssync_staging_url"><?php _e('Staging Site URL', 'tss'); ?></label>
                    <input name="ssync_staging_url" id="ssync_staging_url" type="text"
                           value="<?php echo esc_attr(get_option('ssync_staging_url', '')); ?>"
                           class="regular-text code">
                    <span class="field-info"><?php _e('Enter the full URL of your staging site', 'tss'); ?></span>
                </div>
                <div class="form-field staging <?php if (get_option('ssync_mode', 'live') ==
                    'live') echo 'hidden'; ?>">
                    <label for="ssync_live_url"><?php _e('Live Site URL', 'tss'); ?></label>
                    <input name="ssync_live_url" id="ssync_live_url" required type="text" onchange="validate();"
                           value="<?php echo esc_attr(get_option('ssync_live_url', '')); ?>" class="regular-text code">
                    <span class="field-info"><?php _e('Enter the full URL of your live site', 'tss'); ?></span>
                </div>
                <div class="form-field live <?php if (get_option('ssync_mode', 'live') ==
                    'staging') echo 'hidden'; ?>">
                    <label for="ssync_staging_email"><?php _e('Staging Site Admin Email Address', 'tss'); ?></label>
                    <input name="ssync_staging_email" id="ssync_staging_email" type="email"
                           value="<?php echo esc_attr(get_option('ssync_staging_email', '')); ?>"
                           class="regular-text code">
                </div>
                <div class="form-field staging <?php if (get_option('ssync_mode', 'live') ==
                    'live') echo 'hidden'; ?>">
                    <label for="ssync_hours"><?php _e('Elapsed Time for Sync Reminding Alerts', 'tss'); ?></label>
                    <select name="ssync_hours" id="ssync_hours" required type="text">
                        <option value="6" <?php if (get_option('ssync_hours', '12') == '6') echo 'selected="selected"'; ?>>
                            Every 6 hours
                        </option>
                        <option value="12" <?php if (get_option('ssync_hours', '12') == '12') echo 'selected="selected"'; ?>>
                            Every 12 hours
                        </option>
                        <option value="24" <?php if (get_option('ssync_hours', '12') == '24') echo 'selected="selected"'; ?>>
                            Daily
                        </option>
                        <option value="168" <?php if (get_option('ssync_hours', '12') == '168') echo 'selected="selected"'; ?>>
                            Weekly
                        </option>
                    </select>
                </div>

                <div class="notification form-field live <?php if (get_option('ssync_mode', 'live') ==
                    'staging') echo 'hidden'; ?>">
                    <span><?php _e('Send Syncing Notifications', 'tss'); ?></span><input name="ssync_Send"
                                                                                         id="ssync_Send" type="checkbox"
                                                                                         value="yes">
                    <label class="switch" for="ssync_Send"><?php _e('Send Syncing Notifications', 'tss'); ?></label>
                    <!--                        <input type="checkbox" id="switch" /><label for="switch">Toggle</label>-->

                </div>
                <input type="submit" name="submit" id="submit" class="button button-primary"
                       value="<?php if (get_option('ssync_mode', 'live') ==
                           'live') _e('Save Changes', 'tss');
                       else _e('Start Sync', 'tss');
                       ?>">
            </div>
        </form>
    </div>
    <div class="credit">
        <img src="<?php echo SSYNC_ASSETS . '/images/legiit.png' ?>" alt="">
        <h1>
            <?php _e('Why is this free?', 'tss'); ?>
        </h1>
        <p><?php _e('This plugin was developed because we saw this as a common but solveable problem that many designers and developers face when doing things like redesigning websites.', 'tss'); ?></p>
        <p><?php _e('At Legiit, our mission is to help empower people to Get More Stuff Done so be sure to check our world class freelance platform.', 'tss'); ?></p>
        <a href="https://legiit.com/" target="_blank"
           class="button button-primary ssync-btn-legiit"><?php _e('Explore Legiit.com', 'tss'); ?></a>
    </div>
</div>


<script>
    window.isValidURL = false;

    function displayLive() {
        jQuery('.live').removeClass('hidden');
        jQuery('.staging').addClass('hidden');
        jQuery('.live input').attr('required', 'required');
        jQuery('.staging input,.staging select').removeAttr('required');
    }

    function displayStaging() {
        jQuery('.staging').removeClass('hidden');
        jQuery('.live').addClass('hidden');
        jQuery('.staging input,.staging select').attr('required', 'required');
        jQuery('.live input').removeAttr('required');
    }

    async function validate() {
        var url = jQuery('#ssync_live_url').val();
        if (jQuery('[name=ssync_mode]') == 'live' && url.trim() != '') {
            return;
        }

        jQuery('#errorsarea').html('Please wait...');
        jQuery('[name=options_form] input').attr('disabled', 'disabled');

        const result = await jQuery.ajax({
            url: url + 'wp-admin/admin-ajax.php',
            type: 'POST',
            data: {action: 'getmode'}
        });

        if (result === 'live') {
            jQuery('#errorsarea').html('');
            window.isValidURL = true;
            jQuery('[name=options_form] input').removeAttr('disabled');
            jQuery('#login_options_formform').submit();
        } else {
            jQuery('#errorsarea').html('<?php echo wp_kses(__('The site provided has not any Staging Sync plugin set.', 'tss'), []); ?>');
            window.isValidURL = false;
            jQuery('[name=options_form] input[type="text"], input[type="checkbox"], input[type="radio"]').removeAttr('disabled');

        }
    }
</script>