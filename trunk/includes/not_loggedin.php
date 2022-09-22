<style>
    input[type=color], input[type=date], input[type=datetime-local], input[type=datetime], input[type=email], input[type=month], input[type=number], input[type=password], input[type=search], input[type=tel], input[type=text], input[type=time], input[type=url], input[type=week], select, textarea {
        margin-bottom: 5px;
        width: 350px;
        font-size: 11pt;
        padding: .2em .4em;
    }
</style>
<div class="header-section">
    <div class="logo">
        <img width="250" src="<?php echo SSYNC_ASSETS . '/images/ssync.png'?>" alt="">
    </div>
</div>
<h1 class="title"><?php _e('Staging Sync Settings', 'ssync'); ?></h1>
<p><?php _e('In order to use this plugin, you must sign in with your Staging Sync account. If you do not have any TSS account, you can sign up for free.', 'ssync'); ?> </p>
<br><br>
<section class="container text-center">
    <div class="login">
        <!--Login form-->
        <form id="login_form" name="login_form" method="post"
              action="<?php echo esc_attr(filter_input(INPUT_SERVER, 'REQUEST_URI')); ?>">
            <h1><span id="formHeading">
            <?php echo __('Sign In', 'ssync'); ?>
            </h1>
            <?php wp_nonce_field('tss_login', 'tss_login'); ?>
            <p hidden class="well" id="login_validation_errors"></p>

            <input id="login_user" required title="Please introduce your email." type="text" name="login[user]" value=""
                   placeholder="<?php _e('Email', 'ssync'); ?>"></br>

            <input id="login_password" type="password" name="login[password]" value=""
                   placeholder="<?php _e('Password', 'ssync'); ?>"></br></br>


            <input id="login_submit" required title="Please introduce your password." class="button button-primary"
                   type="submit" name="login[submit]" value="<?php _e('Login', 'ssync'); ?>"></br>
<!--            <p class="text-center"><a href="forgot.html">--><?php //_e('Forgot your password?', 'ssync'); ?><!--</a></p>-->

        </form>
        <span>OR</span>
        <!--Signup form-->
        <form name="signup_form" id="signup_form" method="post"
              action="<?php echo esc_attr(filter_input(INPUT_SERVER, 'REQUEST_URI')); ?>">
            <h1><span id="formHeading">
                    <?php echo __('Sign Up', 'ssync'); ?>
            </h1>
            <?php wp_nonce_field('tss_signup', 'tss_signup'); ?>
            <p hidden class="well" id="signup_validation_errors"></p>

            <input id="signup_email" required title="Please introduce your email." type="text" name="signup[email]"
                   value="" placeholder="<?php _e('Email', 'ssync'); ?>"></br>

            <!--<input id="signup_username" type="text" name="signup_username" value="" placeholder="Username or Email"></br>-->

            <input id="signup_password" type="password" required title="Please introduce your password."
                   name="signup[password]" value="" placeholder="<?php _e('Password', 'ssync'); ?>"></br>

            <input id="signup_password_confirm" required title="Passwords must match."
                   onblur="if(this.value!=jQuery('#signup_password').val())this.value='';this.reportValidity();"
                   type="password" name="signup[password_confirm]" value=""
                   placeholder="<?php _e('Confirm Password', 'ssync'); ?>"></br>
            </br>
            <label>
                <input type="checkbox" name="signup[terms_checkbox]" id="signup_terms_checkbox"> I Agree to <a>terms and
                    conditions</a>
            </label></br></br>

            <input class="button button-primary" id="signup_submit" type="submit" name="signup[submit]" value="<?php _e('Sign up', 'ssync'); ?>">

        </form>
        <!--./signup form-->
        <hr>


    </div>

</section>


<script>

    // var formType = 0;

    //function formToggle() {
    //    formType++;
    //    formType = formType % 2;
    //
    //    if (formType === 1) {
    //
    //        jQuery("#login_form").slideUp(250);
    //        jQuery('#login_validation_errors').slideUp(250);
    //        jQuery("#formHeading").html("<?php //echo wp_kses( __( 'Login or </span><a id="formOption" onclick="formToggle()" href="#">Sign Up</a>', 'ssync' ), [] ) ; ?>//");
    //
    //
    //    }
    //
    //    if (formType === 0) {
    //
    //        jQuery("#signup_form").slideUp(250);
    //        jQuery('#signup_validation_errors').slideUp(250);
    //
    //        jQuery("#formHeading").html("<?php //echo wp_kses( __( 'Sign Up or </span><a id="formOption" onclick="formToggle()" href="#">Login</a>', 'ssync' ), [] ) ; ?>//");
    //
    //        jQuery('#login_form').slideDown(250);
    //    }
    //
    //};

</script>