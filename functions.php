<?php

//Add Login/out button on menu



    function wti_loginout_link_menu($items,$args){

        if($args->theme_location == 'primary'){
            if(is_user_logged_in()){

                $items.='<a href="'.wp_logout_url().'">Logout</a>'; 
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
        if( ($page_viewed == "wp-login.php" || $page_viewed == "wp-login") && $_SERVER['REQUEST_METHOD']=="GET" && !(is_user_logged_in()) ){
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
        $atts['subject']= "hi ". $user->user_login. "Your username and password";
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















?>


