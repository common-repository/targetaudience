<?php
function wpta_add_menu_items(){
    if (current_user_can('manage_options')) {
        global $wpta_audience_page_hook;
        $wpta_audience_page_hook = add_options_page(__('Audiences', 'targetaudience'), __('Audiences', 'targetaudience'), 'activate_plugins', 'wpta_audiences_page', 'wpta_render_list_page');
    }
}
add_action('admin_menu', 'wpta_add_menu_items');

function wpta_add_audience_page_scripts($hook){
    if (current_user_can('manage_options')) {
        global $wpta_audience_page_hook;

        if ($wpta_audience_page_hook != $hook)
            return;

        wp_enqueue_script('wpta-audience-page-script', plugins_url('static/js/targetaudience.js', dirname(__FILE__)), array('jquery'), '1.0');
        wp_enqueue_style('wpta-audience-page-style', plugins_url('static/css/targetaudience.css', dirname(__FILE__)), null, '1.0', 'all');

        $params = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce('wpta'),
        );
        wp_localize_script('wpta-audience-page-script', 'wpta', $params);
    }
}
add_action('admin_enqueue_scripts', 'wpta_add_audience_page_scripts');

function wpta_db_change_admin_notice() {
    if (current_user_can('manage_options')) {
        if (!isset($_GET['audience']) || !isset($_GET['action']))
            return;

        if ($_GET['action'] == 'delete') {
            $count = sanitize_text_field(count($_GET['audience']));
            $message = sprintf(_n('%s record deleted from database', '%s records deleted from database', $count, 'targetaudience'), $count);
        }

        if ($_GET['action'] == 'edit') {
            if (isset($_POST['wpta_update_audience'])) {
                if (!empty($_POST['name'])) {
                    $message = __("Audience updated successfully!", "targetaudience");
                } else {
                    $message = __("Audience could not be updated. Please make sure the fields are not empty.", "targetaudience");
                    $class = 'error';
                }
            }
        }

        if(!$message) return;
        ?>
        <div class="updated <?php echo isset($class) ? esc_attr($class) : '' ?>">
            <p><?php echo esc_html($message) ?></p>
        </div>
        <?php
    }
}
add_action( 'admin_notices', 'wpta_db_change_admin_notice' );

function wpta_render_list_page(){
    if(isset($_POST['wpta_add_audience']) || isset($_POST['wpta_update_audience'])) {
        if(empty($_POST['post_nonce']) || !wp_verify_nonce($_POST['post_nonce'],'post_nonce')) return;
    }

    $db = new WPTA_DB();
    ?>
    <div class="wrap">
        <h2><?php _e('Audiences', 'targetaudience') ?></h2>

        <?php if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['audience'])): ?>
            <?php
            if(isset($_POST['wpta_update_audience'])){
                if(!empty($_POST['name'])){
                    $db->update(sanitize_text_field($_GET['audience']), sanitize_text_field($_POST['name']), sanitize_text_field($_POST['alternative_1']), sanitize_text_field($_POST['alternative_2']));
                }
            }
            $audience_item = $db->get(sanitize_text_field($_GET['audience']));
            ?>

            <div class="col-wrap">
                <h2><?php _e("Edit Audience", "targetaudience") ?></h2>
                <form method="POST">
                    <?php wp_nonce_field( 'post_nonce', 'post_nonce'); ?>

                    <table class="form-table">
                        <tbody>
                            <tr class="form-field">
                                <th><label for="name"><?php _e("Name", "targetaudience") ?></label></th>
                                <td>
                                    <input name="name" id="name" size="40" value="<?php echo esc_html($audience_item['name'])?>" type="text" placeholder="<?php _e("entrepreneur", "targetaudience") ?>"/>
                                    <p class="description"><?php _e("The name for this audience (You are entrepreneur?).", "targetaudience") ?></p>
                                </td>
                            </tr>
                            <tr class="form-field">
                                <th><label for="alternative-1"><?php _e("Alternative 1", "targetaudience") ?></label></th>
                                <td>
                                    <input name="alternative_1" id="alternative-1" size="40" value="<?php echo esc_html($audience_item['alternative_1']) ?>" type="text" placeholder="<?php _e("entrepreneurs", "targetaudience") ?>"/>
                                    <p class="description"><?php _e("The first alternative for this audience (We help entrepreneurs).", "targetaudience") ?></p>
                                </td>
                            </tr>
                            <tr class="form-field">
                                <th><label for="alternative-2"><?php _e("Alternative 2", "targetaudience") ?></label></th>
                                <td>
                                    <input name="alternative_2" id="alternative-2" size="40" value="<?php echo esc_html($audience_item['alternative_2']) ?>" type="text" placeholder="<?php _e("entrepreneurs", "targetaudience") ?>"/>
                                    <p class="description"><?php _e("The second alternative for this audience (For entrepreneurs).", "targetaudience") ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php submit_button(__("Update Audience", "targetaudience"), 'primary', 'wpta_update_audience'); ?>
                </form>
            </div>
        <?php else: ?>
            <div id="col-left" class="add-audience-form-container wp-clearfix">
                <div class="col-wrap">
                    <div class="form-wrap">
                        <h2><?php _e("Add an Audience", "targetaudience") ?></h2>
                        <p><?php _e("At first you need to add a few audiences. After that just copy the default shortcode in you page and append the matching identificator to your URL you share.", "targetaudience") ?></p>

                        <form method="POST">
                            <?php wp_nonce_field( 'post_nonce', 'post_nonce'); ?>

                            <div class="form-field form-required">
                                <label for="name"><?php _e("Name", "targetaudience") ?></label>
                                <input name="name" id="name" size="40" type="text" placeholder="<?php _e("entrepreneur", "targetaudience") ?>"/>
                                <p><?php _e("The name for this audience (You are entrepreneur?).", "targetaudience") ?></p>
                            </div>
                            <div class="form-field form-required">
                                <label for="alternative-1"><?php _e("Alternative 1", "targetaudience") ?></label>
                                <input name="alternative_1" id="alternative-1" size="40" type="text" placeholder="<?php _e("entrepreneurs", "targetaudience") ?>"/>
                                <p><?php _e("The first alternative for this audience (We help entrepreneurs).", "targetaudience") ?></p>
                            </div>
                            <div class="form-field form-required">
                                <label for="alternative-2"><?php _e("Alternative 2", "targetaudience") ?></label>
                                <input name="alternative_2" id="alternative-2" size="40" type="text" placeholder="<?php _e("entrepreneurs (too)", "targetaudience") ?>"/>
                                <p><?php _e("The second alternative for this audience (For entrepreneurs).", "targetaudience") ?></p>
                            </div>
                            <div class="form-field">
                            <?php submit_button(__("Add Audience", "targetaudience"), 'primary', 'wpta_add_audience', false); ?>
                            </div>

                            <div id="wpta-info">
                                <h4><?php _e("Important notice", "targetaudience") ?></h4>
                                <p><?php _e("Did you know my exclusive mastermind course? Get two online-marketing hack per week for nearly one year. More than 100 lessons are waiting and access will be granted via invitation only. ", "targetaudience") ?></p>
                                <p><?php printf(__("With this plugin i give you an invitation valued 399 euro. %s", "targetaudience"), '<a href="https://marketerbase.com/r/HYBKHAaVJ6iFAwRayxaZgmUmUgCsujwFJLQEmp9WGw9g" target="_blank" title="'.__("Click here to get your seat now", "targetaudience").'">'.__("Click here to get your seat.", "targetaudience").'</a>'); ?></p>
                                <p><?php _e("PS: Within the course you can get my book &ldquo;Viral-Masterplan&rdquo; (official price 39 euro) for free.","targetaudience"); ?></p>
                                <p class="submit"><a href="https://marketerbase.com/r/HYBKHAaVJ6iFAwRayxaZgmUmUgCsujwFJLQEmp9WGw9g" target="_blank" class="button button-secondary" title="<?php _e("Click here to get your seat now", "targetaudience") ?>"><?php _e("Take invitation now", "targetaudience") ?></a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="col-right" class="audiences-table-container">
                <div class="col-wrap">
                    <form method="get">
                        <input type="hidden" name="page" id="wpta_page" value="<?php echo esc_html($_REQUEST['page']) ?>" />
                        <div class="table-wrap">
                            <?php
                                $audiencesTable = new WPTA_Table();
                                $audiencesTable->prepare_items();
                                $audiencesTable->display();
                            ?>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
}