<?php
/*Template Name: Page with custom register
 
 */
?>
 <?php get_header();?>

<?php
    global $wpdb;

   if(!is_user_logged_in()){

       // ob_start();
        //do_action('topmost_register');

        $errors=array();
        
        if($_SERVER['REQUEST_METHOD'] == "POST"){

            //Username validation
            $username=$wpdb->escape($_REQUEST['username']);

            if(empty($username)){

                $errors['username']= 'Please enter a username';
                
            }

            elseif(strpos($username," ")!== false){
                    $errors['username']='No spaces allowed within username';

                
            }

            elseif(username_exists($username)){
                    $errors['username']='Username exists, please try another';
            }
            
           
            //Email validation

            $email=$wpdb->escape($_REQUEST['email']);

            if(empty($email) || !is_email($email)){
                $errors['email']='Please enter a valid email';
            }
            elseif(email_exists($email)){
                $errors['email']='This email is already in use';
            }


            //password validation
            if(empty($_POST['password'])){
                $errors['password']="Please enter a valid password";
            }
            elseif(0==preg_match("/.{6,}/",$_POST['password'])){
                $errors['password'] = "Password must be at least six characters";
            }
            elseif(0!=strcmp($_POST['password'], $_POST['password_confirmation'])){
                $errors['password']='Passwords do not match';
            }

            $password=$wpdb->escape($_REQUEST['password']);

            //terms validation
            if($_POST['terms'] != "Yes"){
                $errors['terms']="You must agree to terms of service";
            }


            if(count($errors)==0){

                $password=$_POST['password'];
                $new_user_id=wp_create_user($username,$password,$email);
                if(!is_wp_error($new_user_id)){
                    $success=1;
                    
                    //wp_redirect(get_bloginfo('url').'/login/?success=1&u=' . $username);
                    //header( 'Location:' . get_bloginfo('url') . '/login/?success=1&u=' . $username );
                    
                    //wp_redirect(home_url('/login/?success=1&u='.$username));  /***If want to redirect to login page and let user enter password */
                    
                    /*Login directly after registration and redirect to home page
                    $creds=array();
                    $creds['user_login']=$username;
                    $creds['user_password']=$password;
                    $creds['remember']=true;
                    $user=wp_signon($creds,is_ssl() );
                    if(is_wp_error($user)){
                        echo $user->get_error_message;
                    } 
                    else{
                        wp_redirect(home_url());

                    }   */                                                                                   

                }
               
            }

           // do_action('end_register');
           // ob_end_flush();
           

        }
    }


?>

 <form id="wp-signup-form" action="<?php echo esc_url($_SERVER['REQUEST_URI'])?>" method="post">
    
    <ul class="form-errors">
        <?php 
            foreach($errors as $err){
                echo "<li>{$err}</li>";
            }
            echo "</ul>";
        ?>
    </ul>
    <label for ="username">Username</label>
    <input type="text" name="username" id="username" value="<?php echo $username;?>" />
    <label for="email">Email</label>
    <input type="email" name="email" id="email" value="<?php echo $email;?>" />
    <label for="password"> Password</label>
    <input type="password" name="password" id="password" />
    <label for="password_confirmation"> Password</label>
    <input type="password" name="password_confirmation" id="password_confirmation" />
    <input type="checkbox" name="terms" id="terms" value="Yes" />
    
    <label for="terms">I agree to terms of service</label>
    <input type="submit" id="submitbtn" name="submit" value="Sign Up" />
</form>


 <?php get_footer();?>