<?php
/**
 * Provide a admin area view for the plugin
 */
?>
<!-- Create a header in the default WordPress 'wrap' container -->

<div class="wrap">
    <div class="read-me-later"></div>
    <h3><?php _e('Settings','read-me-later'); ?></h3>
    <?php settings_errors(); ?>
    <?php
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
    ?>

    <div class="read-me-later-tabs">
        <div class="row">
            <div class="col s12 teal">
                <ul class="tabs">
                    <li class="tab col s2"><a class="<?php echo $active_tab == 'general' ? 'active' : ''; ?>" href="?page=read-me-later&tab=general"><?php _e('General','read-me-later');?></a></li>
                    <li class="tab col s2"><a class="<?php echo $active_tab == 'display' ? 'active' : ''; ?>" href="?page=read-me-later&tab=display"><?php _e('Display','read-me-later');?></a></li>
                    <li class="tab col s2"><a class="<?php echo $active_tab == 'shortcode' ? 'active' : ''; ?>" href="?page=read-me-later&tab=shortcode"><?php _e('Shortcode','read-me-later');?></a></li>
                </ul>
            </div>
            <div class="read-me-later-table col s12">
                <form method="post" action="options.php">
                    <?php
                    if ($active_tab == 'general') {
                        settings_fields('read-me-later-settings');
                        do_settings_sections('read-me-later-settings');
                    } else if ($active_tab == 'display') {
                        
                        settings_fields('read-me-later-display-settings');
                        do_settings_sections('read-me-later-display-settings');
                    }else{ ?>
                                <blockquote>
                                <h5><?php _e( 'You can display Read Me Later button in any post or page by pasting this shortcode', 'read-me-later' ); ?></h5>
                                <h5>[Readmelater]</h5>
                              </blockquote>
                        <?php }
                    submit_button();
                    ?>
                </form>
            </div>
        </div>
    </div><!-- /.wrap -->

