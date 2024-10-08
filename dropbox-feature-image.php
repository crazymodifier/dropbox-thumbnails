<?php

/**
 * Plugin Name: Dropbox Featured Image
 * Description: Dropbox Featured Image.
 * Version: 1.0
 * Author: Suraj Kumar
 */

session_start();

/**
 * Register meta boxes.
 */
function kdw_register_meta_boxes() {
    add_meta_box( 'kdw', __( 'Upload Image', 'kdw' ), 'kdw_display_callback', 'post','side' );
}
add_action( 'add_meta_boxes', 'kdw_register_meta_boxes' );



// Displaying form in meta box
function kdw_display_callback($post){
    include plugin_dir_path( __FILE__ ) . './form.php';
}


// Adding wordpress media selector
add_action( 'admin_footer', 'media_selector_print_scripts' );

function media_selector_print_scripts() {

	$my_saved_attachment_post_id = get_option( 'media_selector_attachment_id', 0 );

	?><script type='text/javascript'>

		jQuery( document ).ready( function( $ ) {

			// Uploading files
			var file_frame;
			// var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
			var set_to_post_id = <?php echo $my_saved_attachment_post_id; ?>; // Set this

			jQuery('#upload_image_button').on('click', function( event ){

				event.preventDefault();

				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					// Set the post ID to what we want
					file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
					// Open frame
					file_frame.open();
					return;
				} else {
					// Set the wp.media post id so the uploader grabs the ID we want when initialised
					wp.media.model.settings.post.id = set_to_post_id;
				}

				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media({
					title: 'Select a image to upload',
					button: {
						text: 'Use this image',
					},
					multiple: false	// Set to true to allow multiple files to be selected
				});

				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					// We set multiple to false so only get one image from the uploader
					attachment = file_frame.state().get('selection').first().toJSON();

					// Do something with attachment.id and/or attachment.url here
					$( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
					$( '#image_attachment_id' ).val( attachment.id );
                    console.log(attachment);
					// Restore the main post ID
					wp.media.model.settings.post.id = wp_media_post_id;
				});

					// Finally, open the modal
					file_frame.open();
			});

			// Restore the main ID when the add media button is pressed
			jQuery( 'a.add_media' ).on( 'click', function() {
				wp.media.model.settings.post.id = wp_media_post_id;
			});
		});

	</script><?php

}



// Adding plugin setting options
add_action('admin_menu', 'dropbox_featured_image_page');
function dropbox_featured_image_page() {
 
    add_options_page('Dropbox Featured Image', 'Dropbox Featured Image Options Setting', 'manage_options', 'dropbox-featured-image', 'dropbox_featured_image_callback');
    
    // dropbox_featured_image_callback is the function in which I have written the HTML for my custom plugin form.
}



function dropbox_featured_image_callback() { 

    $key = get_option('dropbox_app_key');
    $secret = get_option('dropbox_app_secret');
    print_r(get_option('dropbox_access_token'));
    $uri = urlencode(get_option('dropbox_redirect_uri'));
    if(isset($_GET['code']) && !empty($_GET['code'])){
        $response = set_access_token($_GET['code']);
        if($response){
            
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e( 'Done!', 'sample-text-domain' ); ?></p>
            </div>
            <?php
            
        }
        else{
            print_r($response);
        }
    }
    // print_r(admin_url( 'options-general.php?page=dropbox-featured-image' )) ;
    ?>
    <div class="wrap">
        <h2>Dropbox Featured Image Options</h2>

        <h3>Documentation</h3>
        <ol>

            
            <li>Create a app in your dropbox account.
                <div>
                    <img src="<?php echo plugin_dir_url( __FILE__ )?>doc/create.png" width="500" alt="">
                </div>
            </li>
            <li>
                Copy App key and secret key. insert below form.
                <div>
                    <img src="<?php echo plugin_dir_url( __FILE__ )?>doc/key.png" width="500" alt="">
                </div>
            </li>
            <li>
                Copy current page URL and use it as REDIRECT URI 
                <div>
                    <img src="<?php echo plugin_dir_url( __FILE__ )?>doc/redirect_uri.png" width="500" alt="">
                </div>
            </li>
            <li>
                Click on "save change" button to save your information to DB. and then click "Get token" button to get authorization.

                <div>
                    <img src="<?php echo plugin_dir_url( __FILE__ )?>doc/autho.png" width="500" alt="">
                </div>
            </li>
            <li>
                Now add new post, and choose your image.
                <div>
                    <img src="<?php echo plugin_dir_url( __FILE__ )?>doc/choose.png" width="500" alt="">
                </div>
            </li>
            <li>

                click on Checkbox to make this image as post thumbnail
                <div>
                    <img src="<?php echo plugin_dir_url( __FILE__ )?>doc/checked.png" width="500" alt="">
                </div>
            </li>
        </ol>

        <hr>

        <form method="post" action="options.php">
            <?php settings_fields('dropbox_featured_image'); ?>
 
        <table class="form-table">
 
            <tr>
                <th><label for="dropbox_app_key_id">Dropbox App Key:</label></th>
 
                <td>
<input type = 'text' class="regular-text" id="dropbox_app_key_id" name="dropbox_app_key" value="<?php echo $key ; ?>">
                </td>
            </tr>
 
            <tr>
                <th><label for="dropbox_app_secret_id">Dropbox App Secret:</label></th>
                <td>
<input type = 'password' class="regular-text" id="dropbox_app_secret_id" name="dropbox_app_secret" value="<?php echo $secret; ?>">
                </td>
            </tr>
 
            <tr>
                <th><label for="dropbox_redirect_uri_id">Redirect URI:</label></th>
   <td>
<input type = 'url' class="regular-text" id="dropbox_redirect_uri_id" name="dropbox_redirect_uri" value="<?php echo urldecode($uri); ?>">
                </td>
            </tr>
        </table>
 
        <?php submit_button();
        
        ?>

        <?php

        if(!empty($key)) { ?>
        <a href="https://www.dropbox.com/oauth2/authorize?client_id=<?php echo $key?>&redirect_uri=<?php echo $uri?>&response_type=code&token_access_type=offline" class="button">Get Token</a>

        <?php } ?>
    </div>
<?php } 



function dropbox_featured_image_settings() {
 
    register_setting('dropbox_featured_image', 'dropbox_app_key');
  
    register_setting('dropbox_featured_image', 'dropbox_app_secret');
  
    register_setting('dropbox_featured_image', 'dropbox_redirect_uri');
    register_setting('dropbox_featured_image', 'dropbox_access_token');
    register_setting('dropbox_featured_image', 'dropbox_refresh_token');
  
 }
add_action('admin_init', 'dropbox_featured_image_settings');
// Setting options



// hooking when the post will save
add_action( 'save_post', function($post_id){


    $att_id = $_REQUEST['image_attachment_id'];
    if(isset($_REQUEST['dropbox_featured_image'])){
        set_post_thumbnail($post_id, $att_id);
        update_post_meta($post_id, 'dropbox_featured_image', true);
    }
    else{
        update_post_meta($post_id, 'dropbox_featured_image', '');
    }

    update_post_meta($post_id, 'dropbox_featured_image_id',$att_id);
    
    //Uploading file to drop box
    $filename = get_attached_file($att_id);

    $access_token = get_option('dropbox_access_token');

    $api_url = 'https://content.dropboxapi.com/2/files/upload'; //dropbox api url

    $headers = array('Authorization: Bearer '. $access_token,
        'Content-Type: application/octet-stream',
        'Dropbox-API-Arg: '.
        json_encode(
            array(
                "path"=> '/'. basename($filename),
                "mode" => "add",
                "autorename" => true,
                "mute" => false
            )
        )

    );

    $ch = curl_init($api_url);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);

    $path = $filename;
    $fp = fopen($path, 'rb');
    $filesize = filesize($path);

    curl_setopt($ch, CURLOPT_POSTFIELDS, fread($fp, $filesize));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return;
});



// Setting to access token in session

function set_access_token($code){
    $key = get_option('dropbox_app_key');
    $secret = get_option('dropbox_app_secret');
    $uri = urlencode(get_option('dropbox_redirect_uri'));

    $code = $_GET['code'];
    
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.dropbox.com/oauth2/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "code=$code&grant_type=authorization_code&redirect_uri=$uri");
    curl_setopt($ch, CURLOPT_USERPWD, $key . ':' . $secret);

    $headers = array();
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    $result = json_decode($result);

    if(!empty($result->access_token)){
        $access_token = $result->access_token;
        $refresh_token = $result->refresh_token;

        update_option( 'dropbox_access_token', $access_token );
        update_option( 'dropbox_refresh_token', $refresh_token );
        return true;
    }
    else{
        return false;
    }
    
}

add_filter( 'cron_schedules', 'isa_add_every_3_hours' );
function isa_add_every_3_hours( $schedules ) {
    $schedules['every_3_hours'] = array(
            'interval'  => 60 * 60 * 3,
            'display'   => __( 'Every 3 hour', 'textdomain' )
    );
    return $schedules;
}

// Schedule an action if it's not already scheduled
if ( ! wp_next_scheduled( 'isa_add_every_3_hours' ) ) {
    wp_schedule_event( time(), 'every_3_hours', 'isa_add_every_3_hours' );
}

// Hook into that action that'll fire every five minutes
add_action( 'isa_add_every_3_hours', 'every_3_hours_event_func' );
function every_3_hours_event_func() {
    // do something here you can perform anything
    $key = get_option('dropbox_app_key');
    $secret = get_option('dropbox_app_secret');
    $refresh_token = get_option('dropbox_refresh_token');
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.dropbox.com/oauth2/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=refresh_token&refresh_token=$refresh_token");
    curl_setopt($ch, CURLOPT_USERPWD,  $key .':' . $secret);

    $headers = array();
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    
    curl_close($ch);

    $result = json_decode($result);

    $access_token = $result->access_token;
    $refresh_token = $result->refresh_token;
    update_option( 'dropbox_access_token', $access_token );
}