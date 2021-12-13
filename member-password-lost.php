<?php
/*Template Name: Page with custom lost password page
 
 */
?>
 <?php get_header();?>
 
 <div id="password-lost-form" class="widecolumn">
    
    <p>
        
        Enter your email address and we'll send you a link you can use to pick a new password.
             
    </p>
 
    <form id="lostpasswordform" action="<?php echo wp_lostpassword_url(); ?>" method="post">
        <p class="form-row">
            <label for="user_login">Email</label>
            <input type="text" name="user_login" id="user_login">
        </p>
 
        <p class="lostpassword-submit">
            <input type="submit" name="submit" class="lostpassword-button"
                   value='Reset Password'/>
        </p>
    </form>
</div>

<?php get_footer();?>