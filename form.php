<div class="">

    <?php

    $url = $checked ='';

    if(isset($_GET['post']) && isset($_GET['action']) && $_GET['action'] == 'edit'){
        $id = get_post_meta($_GET['post'],'dropbox_featured_image_id',1);
        $url = wp_get_attachment_url($id);

        $checked = get_post_meta($_GET['post'], 'dropbox_featured_image', true);

        if($checked){
            $checked = 'checked';
        }
    }

    ?>


    <div class='image-preview-wrapper'>
        <img id='image-preview' src='<?php echo $url; ?>' height='100'>
    </div>
    <label for="">
        <input type="checkbox" <?php echo $checked ?> name="dropbox_featured_image" id="dropbox_featured_image_id">
        Use as featured Image
    </label><br><br>
    <div class="">

        <input id="upload_image_button" type="button" class="button" value="<?php _e( 'Choose image' ); ?>" />
    </div>
    <input type='hidden' name='image_attachment_id' id='image_attachment_id' value='<?php echo $id; ?>'>

        
</div>