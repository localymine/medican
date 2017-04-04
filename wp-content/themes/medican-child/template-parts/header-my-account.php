<?php
global $current_user;
wp_get_current_user();
?>
<div class="header-my-account <?php print (is_user_logged_in() ? 'logged-in' : '') ?>">
	<?php if (is_user_logged_in()): ?>
	<label class="vendor-overlay"></label>
	<?php endif; ?>
    <div class="dropdown">
        <input id="login-register-toggle" type="checkbox" style="position: absolute; clip: rect(0, 0, 0, 0);">
        <div class="link">
            <a class="outerclass">
                <span><?php print (is_user_logged_in() ?  esc_html__('Signed in', 'medican'): esc_html__('Sign in', 'medican')) ?></span>
                <?php 
               if(is_user_logged_in())
               { ?>
               <i class="fa fa-lock" aria-hidden="true"></i>
               <?php  }
                ?>
               <?php print (is_user_logged_in() ? get_avatar($current_user->ID) : '') ?>            
            </a>            
            <label class="customtrigger" for="login-register-toggle"></label>
        </div>
        <?php if (is_user_logged_in()): ?>
            <?php
            $type = 'AZEXO_Dashboard_Links';
            global $wp_widget_factory;
            if (is_object($wp_widget_factory) && isset($wp_widget_factory->widgets, $wp_widget_factory->widgets[$type])) {
                the_widget($type, array('title' => esc_html__('My account', 'medican')));
                ?>
           <div style ='display:none' id='login_form' class="form duplicate_form_login">
                 <?php if (function_exists('wc_get_page_permalink') && !azexo_is_current_post(wc_get_page_id('myaccount'))): ?>
                   <?php
                    wp_enqueue_script('wc-password-strength-meter');
                    if (function_exists('wc_get_template')) {
                        wc_get_template('myaccount/form-login.php');
                    }
                endif;
                ?>
               </div>
          <?php  }
            ?>
           <?php else: ?>
            <div id='login_form' class="form">
               <?php if (function_exists('wc_get_page_permalink') && !azexo_is_current_post(wc_get_page_id('myaccount'))): ?>
                    <?php
                    wp_enqueue_script('wc-password-strength-meter');
                    if (function_exists('wc_get_template')) {
                        wc_get_template('myaccount/form-login.php');
                    }
                endif;
                ?>
            </div>
        <?php endif; ?>
    </div>    
</div>
