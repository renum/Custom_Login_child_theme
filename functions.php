<?php

/**** Add menu items -start */

        //Add Login/out button on menu


            function wti_loginout_link_menu($items,$args){

                if($args->theme_location == 'primary'){
                    if(is_user_logged_in()){

                        
                        $items.='<li class="main-nav-welcm">Welcome '. get_userdata(get_current_user_id())->user_login.'!'.'<div class="sub-content"><a href="">My Profile</a>'.
                        '<a href="">My Classes</a><a href="'.wp_logout_url().'">Logout</a></div></li>';
                    }
                    
                    else{
                        //$items.='<a href="'.wp_login_url(get_permalink()).'">Custom Login</a>';   //goes to default wp-login page
                        //echo 'login link is'.get_page_link(get_page_by_title('loginc'));
                        $items.='<a href="'.esc_url(get_page_link(get_page_by_title('login'))).'">Login</a>';   //goes to custom login page
                    }
                    
                    
                }

                return $items;

            }

            add_filter('wp_nav_menu_items','wti_loginout_link_menu',10,2);

        //Add Register button on custom login form. Utilise form bottom, top or middle


            function add_register_button(){

                
                //echo $register_page;
                //$register_button='<p>New User. Register here.<button id="register" onclick=location.href=\'http://musicclass.local/registercustom/\';>Register</button></p>';
                $register_button='<p>New User. Register here.<button id="register">Register</button></p>';
                return $register_button;

            }

            add_action('login_form_bottom', 'add_register_button');


/**** Add menu items -end */    


/***Redirects- start */
        //Redirect to custom login page instead of wp-login when logged out


            function logout_page(){

                $login_page=home_url('/login');
                wp_redirect($login_page . '?login=false');
                exit;


            }
            add_action('wp_logout','logout_page');

        //Redirect to custom login page when wp-login.php is accessed. wp-admin still accessible

            function redirect_login_page(){
                $login_page=home_url('/login');
                $page_viewed=basename(esc_url($_SERVER['REQUEST_URI']));
                //echo $_SERVER['REQUEST_URI'];
                //if( ($page_viewed == "wp-login.php" || $page_viewed == "wp-login") && $_SERVER['REQUEST_METHOD']=="GET" && !(is_user_logged_in()) && strpos('action=rp',$_SERVER['REQUEST_URI']) == 0)
                if( ($page_viewed == "wp-login.php" || $page_viewed == "wp-login") && $_SERVER['REQUEST_METHOD']=="GET" && !(is_user_logged_in()) )
                
                
                {
                    wp_redirect($login_page);
                    exit;

                }
                if( ($page_viewed == "wp-login.php" || $page_viewed == "wp-login") && $_SERVER['REQUEST_METHOD']=="GET" && is_user_logged_in() ){
                    wp_redirect(home_url());
                    exit;

                }
            }
            add_action( 'init','redirect_login_page');



        //Redirect to home page if user is logged in and trying to access register page
            function redirect_home_page(){
                $home_page=home_url();
            
                $page_viewed=basename(esc_url($_SERVER['REQUEST_URI']));

                if(($page_viewed == "register.php"|| $page_viewed=="register") && is_user_logged_in()){
                    wp_redirect($home_page);
                    exit;

                }
            }
            add_action('init','redirect_home_page');

        //Redirect to custom login page instead of wp-login when any errors
            function verify_username_password( $user, $username, $password ) {
                
                $login_page  = home_url( '/login/' );
                if( $username == "" || $password == "")  {
                    
                    wp_redirect( $login_page . '?login=empty');
                    exit;
                }
                
                
            }
            add_filter( 'authenticate', 'verify_username_password', 1,3);

            function login_failed() {
                $login_page  = home_url( '/login/' );
                wp_redirect( $login_page . '?login=failed' );
                
               exit;
            
               
            }
            add_action( 'wp_login_failed', 'login_failed' );




/***Redirects- end */  
  


//Enqueue scripts and styles


    function my_theme_enqueue_styles_scripts() {

        wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css',
        array(),  // if the parent theme code has a dependency, copy it to here
        wp_get_theme()->parent()->get('Version'));
        
        wp_enqueue_style( 'child-style', get_stylesheet_uri(),array('parent-style'),
        wp_get_theme()->get('Version'));  // this only works if you have Version in the style header
        
        if(is_page('login')){
            wp_enqueue_script('loginscript',get_template_directory_uri().' - child/js/login.js',array(),null);
        }
        if(is_page('register')){
            wp_enqueue_script('registerscript',get_template_directory_uri().' - child/js/register.js',array(),null);
        }
    }

    add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles_scripts'); 


//Shortcode for custom login form

    //step1
    function custom_login_shortcode(){

        add_shortcode('custom-login-form','custom_login_shortcode_fn');
    }

    //step2

    function custom_login_shortcode_fn(){

        if ( ! is_user_logged_in() ) { // Display WordPress login form:
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
                'value_username' => NULL,
                'value_remember' => true
        
            );
            wp_login_form( $args );
        }

    }



    //step3

    add_action('init', 'custom_login_shortcode');

    


//Start buffering to avoid redirect issues after successful registration

    function callback($buffer) {
        // You can modify $buffer here, and then return the updated code
        return $buffer;
    }
    function buffer_start() { 
        ob_start("callback");
    }
    function buffer_end() { 
        ob_end_flush(); 
    }
    // Add hooks for output buffering
    add_action('init', 'buffer_start');
    add_action('wp_footer', 'buffer_end');




//Disable admin bar. But dashboard still visible to user if go to wp-admin
    add_action('after_setup_theme', 'remove_admin_bar');

    function remove_admin_bar() {
        if (!current_user_can('administrator') && !is_admin()) {
            show_admin_bar(false);
        }
    }


    //* Custom register email after user is created*/

    function send_welcome_email_to_new_user($user_id) {
        $user = get_userdata($user_id);
        $user_email = $user->user_email;
        // for simplicity, lets assume that user has typed their first and last name when they sign up
        $user_full_name = $user->user_firstname . $user->user_lastname;
        $atts=array();

        // Now we are ready to build our welcome email
        $atts['to'] = $user_email;
        $atts['subject']= "hi   ". $user->user_login. "Your username and password";
        $atts['message'] = '
                <h1>Dear ' . $user->user_login . ',</h1></br>
                <p>Thank you for joining our site. Your account is now active.</p>
                <p>Please go ahead and navigate around your account.</p>
                <p>Let me know if you have further questions, I am here to help.</p>
                <p>Enjoy the rest of your day!</p>
                <p>Kind Regards,</p>
                <p>Musicclassesforall</p>
        ';
        $atts['message']=$atts['message']."\n".'<a href="'.wp_login_url().'">Login Here</a>';
        $atts['headers'] = array('Content-Type: text/html; charset=UTF-8');

        // Create array that can be picked up later for the email.
        global $my_array;
        $my_array['ID'] = $user_id;

        // Get the username, add it to the array.
        if ( isset(  $_POST['user_login'] ) ) {
            $my_array['user_login'] =  $_POST['user_login'];
        } else {
            $user_info = get_userdata( $user_id );
            $my_array['user_login'] = $user_info->user_login;
        }

        // Create an activation key, add to array.
        $my_array['activation_key'] = md5( microtime() . rand() );
        update_user_meta( $user_id, 'auto_log_key', $my_array['activation_key'] );



        if (wp_mail($atts['to'],$atts['subject'],$atts['message'],$atts['headers'])) {
        error_log("email has been successfully sent to user whose email is " . $user_email);
        }else{
        error_log("email failed to sent to user whose email is " . $user_email);
        }
    }

    // Attach above function to user_register action hook
    add_action('user_register', 'send_welcome_email_to_new_user');
  

    /*Add login info to Login link*/

    add_filter( 'wp_mail', 'set_up_auto_login_link' );
    function set_up_auto_login_link( $atts ) {

        //var_dump($atts);
    
        // Check if email subject contains "Your username and password".
        if ( isset ( $atts ['subject'] ) && strpos( 'Your username and password',$atts['subject'])!==0 ) {
            if ( isset( $atts['message'] ) ) {
    
                // Pick up the global array of user info from 'user_register' action.
                global $my_array;
    
                // Assemble the data for the query string.
                $qstr = '?id=' . $my_array['ID'] . '&u=' . $my_array['user_login'] . '&k=' . $my_array['activation_key'];
    
                // Prepare data for search/replace on the login link (to add the query string).
                $old = '/wp-login.php';
                $new = '/wp-login.php' . $qstr;
    
                // Replace the original login link with the new one containing query string data for auto login.
                $atts['message'] = str_replace( $old, $new, $atts['message'] );
                //error_log($atts['message']);
            }
        }
    
        return $atts;
    }


    

    /*Enable auto login by parsing the strings sent in email link*/

    add_action( 'init', 'auto_log_user_in' );
    function auto_log_user_in() {

        // If ID, user, and key are all present.
        if ( isset( $_GET['id'] ) 
        && isset( $_GET['u'] ) 
        && isset( $_GET['k'] ) ) {

            // Get the query string values.
            $user_id    = $_GET['id'];
            $user_login = $_GET['u'];
            $activation = $_GET['k'];

            // Get the user data for validation.
            $chk_user = get_user_by( 'id', $user_id );

            // If a user is returned and it's not an admin, validate and login.
            if ( $chk_user && ! user_can( $chk_user->ID, 'manage_options' ) ) {

                if ( $chk_user->user_login == $user_login && $chk_user->auto_log_key == $activation ) {

                    wp_set_current_user( $chk_user->ID, $user_login );
                    wp_set_auth_cookie( $chk_user->ID );
                    do_action( 'wp_login', $user_login );
                    wp_redirect( home_url() );
                    exit();

                }
            }
        }
    }

//Forgot password functionality

/*Edit the default retrieve password email*/
    //* Password reset activation E-mail -> Body
add_filter( 'retrieve_password_message', 'dnlt_retrieve_password_message', 10, 2 );

function dnlt_retrieve_password_message( $message, $key ){
    remove_filter( 'wp_mail', 'set_up_auto_login_link' );   //This filter originally set for sending autologin link if set at this point changes the forgot password link sent in the email
    //error_log('modifying retrieve mail message');
    $user_data = '';
    // If no value is posted, return false
    if( ! isset( $_POST['user_login'] )  ){
            return '';
    }
    // Fetch user information from user_login
    if ( strpos( $_POST['user_login'], '@' ) ) {

        $user_data = get_user_by( 'email', trim( $_POST['user_login'] ) );
    } else {
        $login = trim($_POST['user_login']);
        $user_data = get_user_by('login', $login);
    }
    if( ! $user_data  ){
        return '';
    }
    
    $user_login = $user_data->user_login;
    $user_email = $user_data->user_email;
    // Setting up message for retrieve password
    $message = "A password reset has been requested for this site:\n\n";
    $message .= network_home_url( '/' ) . "\r\n\r\n";
    $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";  
    $message .= "Change this text to whatever you like.\n\n";
    $message .= "If you did not request this, just ignore this email and nothing will happen.\n\n"; 
    $message .= "To reset your password, visit the following address:\n";
    $message .= site_url("wp-login.php?action=resetpass&key=$key&login=" . rawurlencode($user_login), 'login')."\r\n";
    //$message .= site_url("wp-login.php?action=rp&key=$key&login=$user_login");
   
    return $message;
}

//*****Forgot password customization *//
/***Redirect to the custom forgot password page when link from wp_lostpassword_url() is accessed */

add_action('login_form_lostpassword','redirect_to_custom_lost_password');

function redirect_to_custom_lost_password(){

    if($_SERVER['REQUEST_METHOD']=='GET'){
        if(is_user_logged_in()){
            wp_redirect(home_url());
            exit;
        }
        wp_Redirect(home_url('member-password-lost'));
        exit;
    }
}

/* Redirect to custom login page with message that email has been sent after sending mail*/
add_action( 'login_form_lostpassword', 'do_password_lost' );

function do_password_lost() {
    if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
        $errors = retrieve_password();  //only checks data  from form and prepare by creating reset token
        if ( is_wp_error( $errors ) ) {
            // Errors found
            $redirect_url = home_url( 'member-password-lost' );
            $redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
        } else {
            // Email sent
            $redirect_url = home_url( 'login' );
            $redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
        }
 
        wp_redirect( $redirect_url );
        exit;
    }
}


/**Password reset */
add_action( 'login_form_rp', 'redirect_to_custom_password_reset' );
add_action( 'login_form_resetpass', 'redirect_to_custom_password_reset');

function redirect_to_custom_password_reset(){
    
    var_dump($_SERVER);
    var_dump($_GET);
    var_dump($_REQUEST); 
    var_dump($_COOKIE); 
    echo COOKIEHASH;

    
    if($_SERVER['REQUEST_METHOD'] =='GET'){

        list( $rp_path ) = explode( '?', wp_unslash( $_SERVER['REQUEST_URI'] ) );
		$rp_cookie       = 'wp-resetpass-' . COOKIEHASH;

		if ( isset( $_GET['key'] ) && isset( $_GET['login'] ) ) {
			$value = sprintf( '%s:%s', wp_unslash( $_GET['login'] ), wp_unslash( $_GET['key'] ) );
			setcookie( $rp_cookie, $value, 0, $rp_path, COOKIE_DOMAIN, is_ssl(), true );

			wp_safe_redirect( remove_query_arg( array( 'key', 'login' ) ) );
			exit;
		}

        
        if ( isset( $_COOKIE[ $rp_cookie ] ) && 0 < strpos( $_COOKIE[ $rp_cookie ], ':' ) ) {
			list( $rp_login, $rp_key ) = explode( ':', wp_unslash( $_COOKIE[ $rp_cookie ] ), 2 );

			$user = check_password_reset_key( $rp_key, $rp_login );
    
            var_dump($user);
        
            if(is_wp_error($user) || ! $user){

                echo $user->get_error_code();   

                if($user && $user->get_error_code() == 'expired_key'){
                    wp_redirect(home_url('login?login=expiredkey'));
                //echo 'expired';
                }
                else{

                     wp_redirect(home_url('login?login=invalidkey')); 
                //echo 'invalid';
                     
                }
                exit;
            }                                                                                           
        }

    
        $redirect_url=home_url('member-password-reset');
        $redirect_url=add_query_arg( 'login',esc_attr($rp_login),$redirect_url );
        $redirect_url=add_query_arg( 'key',esc_attr($rp_key),$redirect_url );
        echo $redirect_url;
        wp_redirect($redirect_url);
       
        
    }
}

add_action( 'login_form_rp', 'do_password_reset' );
add_action( 'login_form_resetpass', 'do_password_reset');

/**
 * Resets the user's password if the password reset form was submitted.
 */
function do_password_reset() {
    if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
        $rp_key = $_REQUEST['rp_key'];
        $rp_login = $_REQUEST['rp_login'];
 
        $user = check_password_reset_key( $rp_key, $rp_login );
 
        if ( ! $user || is_wp_error( $user ) ) {
            if ( $user && $user->get_error_code() === 'expired_key' ) {
                wp_redirect( home_url( 'login?login=expiredkey' ) );
            } else {
                wp_redirect( home_url( 'login?login=invalidkey' ) );
            }
            exit;
        }
 
        if ( isset( $_POST['pass1'] ) ) {
            if ( $_POST['pass1'] != $_POST['pass2'] ) {
                // Passwords don't match
                $redirect_url = home_url( 'member-password-reset' );
 
                $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
                $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
                $redirect_url = add_query_arg( 'error', 'password_reset_mismatch', $redirect_url );
 
                wp_redirect( $redirect_url );
                exit;
            }
 
            if ( empty( $_POST['pass1'] ) ) {
                // Password is empty
                $redirect_url = home_url( 'member-password-reset' );
 
                $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
                $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
                $redirect_url = add_query_arg( 'error', 'password_reset_empty', $redirect_url );
 
                wp_redirect( $redirect_url );
                exit;
            }
 
            // Parameter checks OK, reset password
            reset_password( $user, $_POST['pass1'] );
            wp_redirect( home_url( 'login?password=changed' ) );
        } 
        else {
            echo "Invalid request.";
        }
 
        exit;
    }
}

/****************Shortcodes */

//Add short code to display error on login page

//function show_credential_err_shortcode(){
    add_shortcode('show-credential-error', 'show_credential_error_fn');
//}
function show_credential_error_fn(){
    //echo 'login error';
    //if(is_page('login')){
        $login=(isset($_GET['login'])?$_GET['login']:0);
        $password=(isset($_GET['password'])? $_GET['password']:0);
        $checkemail=(isset($_GET['checkemail'])? $_GET['checkemail']:0);

        if($login === "empty"){
            echo '<p class="login-msg"><strong>Error:</strong>Username and/or password is empty</p>';
        }
        if($login === "failed"){
            echo '<p class="login-msg"><strong>Error:</strong> Invalid username and/or password</p>';
           
    
        }
        if($login==="invalidkey"){
            echo '<p class="login-msg"><strong>Error:</strong>The password reset key is invalid</p>';
        }
        if($password==="changed"){
            echo '<p class="login-msg">Password has been changed successfully. Please proceed to login.</p>';
        }
    
        elseif($checkemail==="confirm"){
            echo '<p class="login-msg">Please check your email to reset the password.</p>';
        }
       
    //}
    

}

//add_action('wp_login_failed', 'show_credential_err_shortcode');





?>
