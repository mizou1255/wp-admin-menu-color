<?php
add_action('admin_menu', 'wpacm_options_page');
function wpacm_options_page() {
    add_submenu_page(
        'tools.php',
        'WP Admin Color',
        'WP Admin Color',
        'manage_options',
        'wpacm_options',
        'display_wpacm_options_page'
    );
}

function display_wpacm_options_page() {
    ?>
    <div class="wrap" id="wpacm">
        <div class="wpacm_section">
            <h2><?php esc_attr_e('WP Admin Menu Color', 'wp-admin-menu-color'); ?></h2>
        </div>

        <div class="wpacm_section">
            <div class="wpacm_loader">
                <div class="wpacm_spinner"></div>
            </div>
            <button id="reset-menu-colors"  class="wpacm__reset"><?php esc_attr_e('Reset', 'wp-admin-menu-color'); ?></button>
            <button id="save-menu-colors" class="wpacm__save"><?php esc_attr_e('Save', 'wp-admin-menu-color'); ?></button>
            <div class="wpacm_alert"></div>
            <div id="wpacm_confirmation_dialog" class="wpacm_confirmation_dialog">
                <div class="wpacm_confirmation_content">
                    <p><?php esc_attr_e('Are you sure you want to reset?', 'wp-admin-menu-color'); ?></p>
                    <button id="wpacm_confirm_reset"><?php esc_attr_e('Yes, I confirm !', 'wp-admin-menu-color'); ?></button>
                    <button id="wpacm_cancel_reset"><?php esc_attr_e('Cancel', 'wp-admin-menu-color'); ?></button>
                </div>
            </div>
            <form method="post" action="options.php" class="wpacm__form">
                <input type="hidden" id="nonce" name="nonce" value="<?php echo wp_create_nonce( 'wpacm_nonce' ); ?>">
                <div class="wpacm_tabs">
                    <input type="radio" class="wpacm_tabs__button" name="tabs_radio" id="induvidual" checked/>
                    <label class="wpacm_tabs__text" for="induvidual"><?php esc_attr_e('Individual Colors', 'wp-admin-menu-color'); ?></label>
                    <div class="wpacm_tabs__content">
                        <?php
                        $menu_colors = get_option('menu_colors_option');
                        global $menu;
                        foreach ($menu as $menu_item) {
                            // Continue if the menu item is separator
                            if (empty($menu_item[0])) {
                                continue;
                            }
                            
                            $menu_slug = $menu_item[2];
                            $menu_title = $menu_item[0];
                            $menu_id = wpacm_get_admin_menu_id($menu_slug);
                            $menu_color = isset($menu_colors[$menu_id]) ? $menu_colors[$menu_id] : ''; 
                            ?>
                            <div class="wpacm_color_field">
                                <label for="menu-color-<?php echo $menu_id; ?>"><?php echo $menu_title; ?> :</label>
                                <input type="text" id="menu-color-<?php echo $menu_id; ?>" class="wpacm-color-field" name="menu-color-<?php echo $menu_id; ?>" value="<?php echo esc_attr($menu_color); ?>" />
                            </div>
                            <?php
                        }
                        ?>
                    </div>

                    <input type="radio" class="wpacm_tabs__button" name="tabs_radio" id="global" />
                    <label class="wpacm_tabs__text" for="global"><?php esc_attr_e('Global Colors', 'wp-admin-menu-color'); ?></label>
                    <div class="wpacm_tabs__content">
                        <?php
                            $sidebar_color = get_option('sidebar_color'); 
                            $adminbar_color = get_option('adminbar_color'); 
                        ?>
                        <div class="wpacm_color_field">
                            <label for="sidebar-color"> <?php esc_attr_e('BgColor Admin SideBar', 'wp-admin-menu-color'); ?>:</label>
                            <input type="text" id="sidebar_color" class="wpacm-color-field" name="sidebar-color" value="<?php echo esc_attr($sidebar_color); ?>" />
                        </div>
                        <div class="wpacm_color_field">
                            <label for="adminbar-color"> <?php esc_attr_e('BgColor Admin Bar', 'wp-admin-menu-color'); ?>:</label>
                            <input type="text" id="adminbar_color" class="wpacm-color-field" name="adminbar-color" value="<?php echo esc_attr($adminbar_color); ?>" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
}
add_action( 'admin_enqueue_scripts', 'wpacm_enqueue_color_picker' );
function wpacm_enqueue_color_picker( $hook_suffix ) {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wpacm-script-handle', AMC_URI . 'assets/scripts.js', array( 'wp-color-picker' ), false, true );
}

function wpacm_get_admin_menu_id($menu_slug) {
    global $menu;
    foreach ($menu as $menu_item) {
        if ($menu_item[2] === $menu_slug || (isset($menu_item[5]) && $menu_item[5] === $menu_slug) || (isset($menu_item[6]) && $menu_item[6] === $menu_slug)) {
            $menu_id = $menu_item[2];
            if (isset($menu_item[5])) {
                $menu_id = $menu_item[5];
            } elseif (isset($menu_item[6])) {
                $menu_id = $menu_item[6];
            }
            return $menu_id;
        }
    }
    return false;
}


function save_menu_colors_callback() {
    $nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
    if ( ! wp_verify_nonce( $nonce, 'wpacm_nonce' ) ) {
        wp_send_json_error(array('status' => 'error', 'log' => __('Error: Nonce verification failed', 'wp-admin-menu-color') ));
        wp_die();
    }

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('status' => 'error', 'log' => __('You don\'t have the necessary authorizations.', 'wp-admin-menu-color') ));
        wp_die();
    }

    $sidebarColor = isset($_POST['sidebarColor']) ? $_POST['sidebarColor'] : '';
    $adminbarColor = isset($_POST['adminbarColor']) ? $_POST['adminbarColor'] : '';
    $menu_colors = isset($_POST['menuColors']) ? $_POST['menuColors'] : array();

    update_option('sidebar_color', $sidebarColor);
    update_option('adminbar_color', $adminbarColor);
    update_option('menu_colors_option', $menu_colors);

    wp_send_json_success(array('status' => 'success', 'log' => __('Data successfully saved.', 'wp-admin-menu-color') ));
    wp_die();
}
add_action('wp_ajax_save_menu_colors', 'save_menu_colors_callback');

function reset_menu_colors_callback() {
    $nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
    if ( ! wp_verify_nonce( $nonce, 'wpacm_nonce' ) ) {
        wp_send_json_error(array('status' => 'error', 'log' => __('Error: Nonce verification failed', 'wp-admin-menu-color') ));
        wp_die();
    }

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('status' => 'error', 'log' => __('You don\'t have the necessary authorizations.', 'wp-admin-menu-color') ));
        wp_die();
    }

    update_option('sidebar_color', '');
    update_option('adminbar_color', '');
    update_option('menu_colors_option', '');

    wp_send_json_success(array('status' => 'success', 'log' => __('Data successfully deleted', 'wp-admin-menu-color') ));
    wp_die();
}
add_action('wp_ajax_reset_menu_colors', 'reset_menu_colors_callback');

function wpacm_menu_color_style() {
    $menu_colors = get_option('menu_colors_option');
    $sidebar_color = get_option('sidebar_color');
    $adminbar_color = get_option('adminbar_color');
    $style = '';
    if ($menu_colors && is_array($menu_colors)) {
        $style .= '<style>';
        foreach ($menu_colors as $menu_id => $menu_color) {
            $style .= '#adminmenu #' . $menu_id . ', #adminmenu #' . $menu_id . '.wp-has-current-submenu a.wp-has-current-submenu { background-color: ' . esc_attr($menu_color) . ' !important; }';
        }
        $style .= '</style>';
    }
    if ($sidebar_color) {
        $style .= '<style>';
        $style .= '#adminmenu, #adminmenuwrap { background-color: ' . esc_attr($sidebar_color) .' !important; }';
        $style .= '</style>';
    }
    if ($adminbar_color) {
        $style .= '<style>';
        $style .= '#wpadminbar { background-color: ' . esc_attr($adminbar_color) .' !important; }';
        $style .= '</style>';
    }
    if ($style) {
        echo $style;  
    }
    
}
add_action('admin_head', 'wpacm_menu_color_style');
?>
