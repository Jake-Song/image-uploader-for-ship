<?php
/*
Plugin Name: Image Uploader
Plugin URI: http://wpjake.com
Description: Attach image upload box for single page.
Author: Jake Song
Version: 1.0.0
Author URI: http://wpjake.com
*/
namespace image_uploader;
function register_metaboxes() {
	add_meta_box(
		'image_metabox',
		'이미지 업로드 박스',
		__NAMESPACE__ . '\image_uploader_callback',
		'ship',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', __NAMESPACE__ . '\register_metaboxes' );
function register_admin_script() {
	global $post;
	if( $post !== null && $post->post_type === "ship" ){
		wp_enqueue_script( 'wp_img_upload', plugin_dir_url( __FILE__ ) . '/image-upload.js', array('jquery', 'media-upload'), '0.0.2', true );
		wp_localize_script( 'wp_img_upload', 'customUploads',
			array(
				'imageData' => get_post_meta( get_the_ID(), 'custom_image_data', true ) )
		 );
	}
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\register_admin_script' );
function image_uploader_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'custom_image_nonce' ); ?>

	<div id="metabox_wrapper">
		<?php
			$custom_image_data = get_post_meta( $post->ID, 'custom_image_data', true );
			$test = 0;
	  if( !empty( $custom_image_data ) ) :
			foreach ($custom_image_data as $key => $image) {
		?>
				<img class="selected" src="<?php echo esc_attr($image['url']); ?>">
		<?php	}
	  endif;
		?>

    <input type="hidden" id="img-hidden-field" name="custom_image_data">
		<input type="button" id="image-upload-button" class="button" value="이미지 추가하기">
		<input type="button" id="image-delete-button" class="button" value="이미지 삭제하기">
	</div>

	<?php
}
function save_custom_image( $post_id ) {
	$is_autosave = wp_is_post_autosave( $post_id );
	$is_revision = wp_is_post_revision( $post_id );
	$is_valid_nonce = ( isset( $_POST[ 'custom_image_nonce' ] ) && wp_verify_nonce( $_POST[ 'custom_image_nonce' ], basename( __FILE__ ) ) );
	// Exits script depending on save status
	if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
		return;
	}
	if ( isset( $_POST[ 'custom_image_data' ] ) ) {
		$image_data = json_decode( stripslashes( $_POST[ 'custom_image_data' ] ) );
$test = 0;
		if ( isset( $image_data ) ) {
				$image_meta = [];
			for( $i = 0; $i < count($image_data); $i++){
				$image_meta[$i] = array( 'id' => intval( $image_data[$i]->id ), 'url' => esc_url_raw( $image_data[$i]->url ) );
			}
		} else {
			$image_meta = [];
		}

		update_post_meta( $post_id, 'custom_image_data', $image_meta );
		$test = 0;
	}
}
add_action( 'save_post', __NAMESPACE__ . '\save_custom_image' );
