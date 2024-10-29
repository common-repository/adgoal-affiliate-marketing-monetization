<?php
/*
Plugin Name: adgoal
Version: 1.0.8
Description: adgoal helps you monetize your content by affiliating your links.

Author: adgoal
Author URI: http://www.adgoal.de
*/

define( 'ADGOAL_MIN_WORDPRESS_REQUIRED', "2.7" );
define( 'ADGOAL_WORDPRESS_VERSION_SUPPORTED', version_compare( get_bloginfo( "version" ), ADGOAL_MIN_WORDPRESS_REQUIRED, ">=" ) );
define( 'ADGOAL_ENABLED', ADGOAL_WORDPRESS_VERSION_SUPPORTED && adgoal_validate_option( 'hash' ) );

function adgoal_script() {
  $hash = get_option( "hash" );
  if( $hash ) {
    ?><script type="text/javascript">
      (function(doc,el) {
      var s = doc.createElement(el);
      s.type = 'text/javascript';
      s.async = true;
      s.src = '//js.smartredirect.de/js/?h=<?=addslashes($hash);?>';
      var r = doc.getElementsByTagName(el)[0];
      r.parentNode.insertBefore(s, r);
      }(document,'script'));
      </script>
<?php
  }
}

//settings
function adgoal_options() {
?>
  <div class="wrap">
    <div class="icon32">&nbsp;</div>
    <h2>adgoal link monetization</h2>
  <?php
    if( ! ADGOAL_WORDPRESS_VERSION_SUPPORTED ) {
  ?>
    <p style="width: 50%;">
      This plugin requires WordPress <?php print ADGOAL_MIN_WORDPRESS_REQUIRED; ?> or newer.
    </p>
  <?php
    } else {
      if( get_option( "is-not-first-load" ) && ! adgoal_validate_option( "hash" ) ) {
    ?>
    <div class="error fade">
      <p>
        <strong>Invalid profile hash.</strong>
        Please provide a valid profile hash to activate.
      </p>
    </div>
  <?php
    }
  ?>
    <p class="instructions">
      To activate please provide a valid profile hash from your adgoal account. Find it here:  
      <a target="_blank" href="https://www.adgoal.de/publisher/areas.html">adgoal.de</a>.
    </p>

    <form method="post" action="options.php">
      <?php settings_fields( "adgoal" ); ?>
      <table class="form-table" style="width: auto;">
        <tr valign="top">
          <th style="width: auto;">Profile hash</th>
          <td>
            <input id="adgoal-hash" type="text" name="hash" value="<?php print get_option( "hash" );?>" class="regular-text" maxlength="32"/>
          </td>
        </tr>

      </table>
      <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" />
      </p>
    </form>

  </div>
<?php
    }
}

//validate
function adgoal_validate_option( $name ) {
  $value = get_option( $name );
  switch( $name ) {
    case 'hash':
        if(preg_match( '/^[0-9a-zA-Z]{8}$/i', $value )!=1)
            return false;
        $fc = file_get_contents("https://www.smartredirect.de/api_v2/CheckProfileHash.php?h=".$value);
        if($fc==0)
            return false;    
  }
  return true;
}

//settings
function adgoal_admin_init() {
  register_setting( "adgoal", "hash", "adgoal_clear_option" );
}

//options
function adgoal_options_menu() {
  $page = add_options_page( "adgoal Options", "adgoal", "manage_options", __FILE__, "adgoal_options" );
}

//clearing
function adgoal_clear_option( $value ) {
  return htmlspecialchars($value);
}

add_option( "hash" );
add_action( "admin_init", "adgoal_admin_init" );
add_action( "admin_menu", "adgoal_options_menu");
if( ADGOAL_ENABLED ) {
  add_action( "wp_footer", "adgoal_script" );
}
?>