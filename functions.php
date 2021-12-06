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

//Disable adin bar
    add_action('after_setup_theme', 'remove_admin_bar');

    function remove_admin_bar() {
        if (!current_user_can('administrator') && !is_admin()) {
            show_admin_bar(false);
        }
    }



?>


