<?php
/*Template Name: Page with custom reset password page
 
 */
?>
 <?php get_header();?>
 <?php  
 
    if ( is_user_logged_in() ) {
        echo 'You are already signed in.';
        //wp_redirect(home_url());
    } else {
        
        if ( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] )  ) {
            $attributes['login'] = $_REQUEST['login'];
            $attributes['key'] = $_REQUEST['key'];
            
            // Error messages
            $errors = array();
            if ( isset( $_REQUEST['error'] ) ) {
                $error_codes = explode( ',', $_REQUEST['error'] );

                foreach ( $error_codes as $code ) {
                    $errors []= $code;
                }
            }
            $attributes['errors'] = $errors;
           // var_dump($attributes);
            
        } else {
                echo 'Invalid password reset link.';
        }
    }
 
 
 ?>
 
 <div id="password-reset-form" class="widecolumn">   
        <h3>Pick a New Password</h3>
    
 
    <form name="resetpassform" id="resetpassform" action="<?php echo site_url( 'wp-login.php?action=resetpass' ); ?>" method="post" autocomplete="off">
        <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( $attributes['login'] ); ?>" autocomplete="off" />
        <input type="hidden" name="rp_key" value="<?php echo esc_attr( $attributes['key'] ); ?>" />
         
        <?php if(isset($attributes['errors'])):?>
            <?php if ( count( $attributes['errors'] ) > 0 ) : ?>
                <?php foreach ( $attributes['errors'] as $error ) : ?>
                    <p>
                        <?php echo $error; ?>
                    </p>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
 
        <p>
            <label for="pass1">New password</label>
            <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />
        </p>
        <p>
            <label for="pass2">Repeat new password</label>
            <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />
        </p>
         
        <p class="description"><?php echo wp_get_password_hint(); ?></p>
         
        <p class="resetpass-submit">
            <input type="submit" name="submit" id="resetpass-button"
                   class="button" value="Reset Password" />
        </p>
    </form>
</div>

<?php get_footer();?>