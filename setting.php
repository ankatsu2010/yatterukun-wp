<div class="wrap">
    <h2><?php _e('Yatterukun Settings', 'yatterukun'); ?></h2>

    <?php if (isset($message)) : ?>
    <div id="setting-error-settings_updated" class="updated settings-error">
        <p><strong><?php echo $message; ?></strong></p>
    </div>
    <?php endif; ?>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content" style="position: relative">
                <div class="stuffbox" style="padding: 0 20px">
                    <form method="POST">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">
                                    <label for="page_slug">
                                        <?php _e('Page slug', 'yatterukun'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input type="text" name="page_slug" value="<?php echo self::getOption('page_slug'); ?>" class="regular-text" />
                                    <p class="description"><?php _e('Slug of the destination page to send POST request.', 'yatterukun'); ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label for="user_name">
                                        <?php _e('User name', 'yatterukun'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input type="text" name="user_name" value="<?php echo self::getOption('user_name'); ?>" class="regular-text" />
                                    <p class="description"><?php _e('"username" field of the POST data.', 'yatterukun'); ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label for="upload_key">
                                        <?php _e('Upload key', 'yatterukun'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input type="text" name="upload_key" value="<?php echo self::getOption('upload_key'); ?>" class="regular-text" />
                                    <p class="description"><?php _e('"uploadkey" field of the POST data.', 'yatterukun'); ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label for="data_name">
                                        <?php _e('Data name', 'yatterukun'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input type="text" name="data_name" value="<?php echo self::getOption('data_name'); ?>" class="regular-text" />
                                    <p class="description"><?php _e('Data name of the multipart/form-fata, not a file name.', 'yatterukun'); ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label for="max_size">
                                        <?php _e('Upload max file size', 'yatterukun'); ?>
                                    </label>
                                </th>
                                <td>
                                    <input type="number" name="max_size" step="1" min="1" id="max_size" placeholder="2" class="small-text" value="<?php echo self::getOption('max_size'); ?>" /> MB
                                    <p class="description"><?php _e("It should be less than the system's upload_max_filesize.", 'yatterukun'); ?></p>
                                    <p class="description"><?php echo "System's upload_max_filesize: " . ini_get('upload_max_filesize'); ?></p>
                                </td>
                            </tr>
                            
                            <tr valign="top">
                                <th scope="row">
                                    <label for="file_types">
                                        <?php _e('Allowed file types', 'yatterukun'); ?>
                                    </label>
                                </th>
                                <td>
                                    <p>
                                        <?php $fileTypes = self::getOption('file_types'); ?>
                                        <?php foreach (static::$_file_extensions as $extension): ?>
                                            <label>
                                                <input type="checkbox" name="file_types[]" value="<?php echo $extension ?>" <?php echo is_array($fileTypes) && in_array($extension, $fileTypes, true) ? 'checked' : ''; ?>> <?php echo $extension ?>
                                                <br>
                                            </label>
                                        <?php endforeach; ?>
                                    </p>
                                    <p class="description"><?php _e('Select allowed file types for POST upload.', 'yatterukun'); ?></p>
                                </td>
                            </tr>
                        </table>
                        <p class="submit">
                            <?php submit_button(null, 'primary', 'submit', false); ?>
                            <?php wp_nonce_field( 'yatterukun_settings_nonce' ); ?>
                        </p>
                    </form>
                </div>
            </div>
            <div id="postbox-container-1" class="postbox-container">
                <div class="postbox">
                    <h2 class="hndle ui-sortable-handle"><strong><?php _e('Information', 'yatterukun'); ?></strong></h2>
                    <div class="inside">
                        <div class="main">
                            <ul>
                                <li class="dashicons-before dashicons-flag" style="color: #82878c">
                                    <a href="https://github.com/ankatsu2010/yatterukun-wp/issues/new" style="text-decoration: none" target="_blank"><?php _e('Report Bug and Issues', 'yatterukun'); ?></a>
                                </li>
                                <li class="dashicons-before dashicons-admin-plugins" style="color: #82878c">
                                    <a href="https://github.com/ankatsu2010/yatterukun-wp" style="text-decoration: none" target="_blank"><?php _e('Github Repository', 'yatterukun'); ?></a>
                                </li>
                                <li class="dashicons-before dashicons-wordpress" style="color: #82878c">
                                    <a href="https://wordpress.org/plugins/yatterukun/" style="text-decoration: none" target="_blank"><?php _e('Plugin Page in WP', 'yatterukun'); ?></a>
                                </li>
                                <li class="dashicons-before dashicons-star-filled" style="color: #82878c">
                                    <a href=https://wordpress.org/support/plugin/yatterukun/reviews/?rate=5#new-post" style="text-decoration: none" target="_blank">
                                        <?php _e('Rate to this plugin', 'yatterukun'); ?>
                                    </a>
                                </li>
                                <li class="dashicons-before dashicons-admin-plugins" style="color: #82878c">
                                    <a href="https://github.com/ankatsu2010/twentyseventeen-child-yatterukun" style="text-decoration: none" target="_blank"><?php _e('Child Theme for TwentySeventeen', 'yatterukun'); ?></a>
                                </li>
                                <li class="dashicons-before dashicons-admin-links" style="color: #82878c">
                                    <a href="https://www.andows.jp/yatterukun-wp" style="text-decoration: none" target="_blank"><?php _e('Official Page', 'yatterukun'); ?></a>
                                </li>
                                <li class="dashicons-before dashicons-heart" style="color: #82878c">
                                    <a href="https://www.paypal.me/yatterukun" style="text-decoration: none" target="_blank"><?php _e('Donation appreciated!', 'yatterukun'); ?></a>
                                </li>
                            </ul>
                            <hr>
                            <p><?php _e('To contribute to this plugin development please go <a href="https://github.com/ankatsu2010/yatterukun-wp" target="_blank">plugin Github repository</a>.', 'yatterukun') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>
