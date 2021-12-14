<?php
/*Template Name: Page with custom login
 
 */
?>

<?php get_header();?>


<?php

    while(have_posts()):the_post();
    the_content();

    //Populate user id after successful registration

    if(isset($_GET['u'])){
        $user=$_GET['u'];
    }
    if(isset($_GET['success'])){

        $success=$_GET['success'];
    }

    if($success == 1){
        echo '<p> You have been registered successfully. Please enter your password to login.</p>';
    }


    //Displaying error messages
    //do_shortcode( ' [show-credential-error] ' );
    /*$login=(isset($_GET['login'])?$_GET['login']:0);
    $password=(isset($_GET['password'])? $_GET['password']:0);
    $checkemail=(isset($_GET['checkemail'])? $_GET['checkemail']:0);

    if($login === "empty"){
        echo '<p class="login-msg"><strong>Error:</strong>Username and/or password is empty</p>';
    }

    elseif($login === "failed"){
        echo '<p class="login-msg"><strong>Error:</strong> Invalid username and/or password</p>';
        

    }
    elseif($login==="false"){
        echo '<p class="login-msg"><strong>Error:</strong>You are logged out</p>';
    }
    elseif($login==="invalidkey"){
        echo '<p class="login-msg"><strong>Error:</strong>The password reset key is invalid</p>';
    }

    elseif($password==="changed"){
        echo '<p class="login-msg">Password has been changed successfully. Please proceed to login.</p>';
    }

    elseif($checkemail==="confirm"){
        echo '<p class="login-msg">Please check your email to reset the password.</p>';
    }
*/
    if ( ! is_user_logged_in() ) { // Display WordPress login form:

        $forgot_link='<div class="forgot-password"><p>Forgot your password. Click here to reset.</p>'.'<button id="forgot" onclick=location.href="'. wp_lostpassword_url(). '">Reset Password</button></div>';
        echo $forgot_link;

        
        
        $args = array(
            'redirect' => home_url(), 
            'form_id' => 'loginform-custom',
            'label_username' => __( 'Username' ),
            'label_password' => __( 'Password' ),
            'label_remember' => __( 'Remember Me' ),
            'label_log_in' => __( 'Log In' ),
            'id_username'    => 'user_login',
            'id_password'    => 'user_pass',
            'id_submit'      => 'wp-submit',
            'remember'       => true,
            'value_username' => $user,
            'value_remember' => true

        );
        wp_login_form( $args );
        
    
    }
    








?>


    
  

  <?php endwhile;?> 

<?php get_footer();?>