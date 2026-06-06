<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }
/**
 * Local Profile Photo (avatar) upload.
 *
 * Adds a "Profile Photo" media-upload field to the WordPress user-profile screen
 * so an avatar can be set by uploading an image directly — no Gravatar account
 * required. The chosen attachment id is stored in the `unysonplus_local_avatar`
 * user meta and, when present, overrides Gravatar everywhere via the core
 * `get_avatar_data` filter (admin, author boxes, comments, the block editor, …).
 *
 * Auto-loaded by Theme_Includes (inc/init.php scans inc/includes/*.php). All code
 * is wrapped in a single function_exists guard so a child theme can ship its own
 * inc/includes/local-avatar.php to replace this wholesale.
 *
 * @package unysonplus-theme
 */

if ( ! function_exists( 'unysonplus_local_avatar_boot' ) ) :

	/** User-meta key holding the uploaded avatar's attachment id. */
	define( 'UNYSONPLUS_LOCAL_AVATAR_META', 'unysonplus_local_avatar' );

	/**
	 * Resolve a user id from any of the identifiers core passes to avatar hooks
	 * (user id, email, WP_User, WP_Post, WP_Comment). Returns 0 when unknown.
	 */
	function unysonplus_local_avatar_user_id( $id_or_email ) {
		if ( is_numeric( $id_or_email ) ) {
			return (int) $id_or_email;
		}
		if ( $id_or_email instanceof WP_User ) {
			return (int) $id_or_email->ID;
		}
		if ( $id_or_email instanceof WP_Post ) {
			return (int) $id_or_email->post_author;
		}
		if ( $id_or_email instanceof WP_Comment ) {
			if ( ! empty( $id_or_email->user_id ) ) {
				return (int) $id_or_email->user_id;
			}
			if ( ! empty( $id_or_email->comment_author_email ) ) {
				$user = get_user_by( 'email', $id_or_email->comment_author_email );
				return $user ? (int) $user->ID : 0;
			}
			return 0;
		}
		if ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
			$user = get_user_by( 'email', $id_or_email );
			return $user ? (int) $user->ID : 0;
		}
		return 0;
	}

	/**
	 * Override Gravatar with the uploaded image when one is set. Filtering
	 * get_avatar_data (rather than the get_avatar HTML) means every avatar
	 * consumer picks it up, not just get_avatar().
	 */
	function unysonplus_local_avatar_data( $args, $id_or_email ) {
		if ( ! empty( $args['force_default'] ) ) {
			return $args; // caller explicitly wants the default avatar
		}
		$user_id = unysonplus_local_avatar_user_id( $id_or_email );
		if ( ! $user_id ) {
			return $args;
		}
		$att_id = get_user_meta( $user_id, UNYSONPLUS_LOCAL_AVATAR_META, true );
		if ( ! $att_id ) {
			return $args;
		}
		$size = ! empty( $args['size'] ) ? (int) $args['size'] : 96;
		$img  = wp_get_attachment_image_src( (int) $att_id, array( $size, $size ) );
		if ( $img ) {
			$args['url']           = $img[0];
			$args['found_avatar']  = true;
			// Drop the srcset core would build from the Gravatar URL.
			$args['url2x']         = $img[0];
		}
		return $args;
	}

	/**
	 * Profile-screen field (own profile + editing another user).
	 */
	function unysonplus_local_avatar_field( $user ) {
		$att_id = get_user_meta( $user->ID, UNYSONPLUS_LOCAL_AVATAR_META, true );
		$url    = $att_id ? wp_get_attachment_image_url( (int) $att_id, array( 96, 96 ) ) : '';
		// Rendered hidden at the bottom of the form (show_user_profile fires there),
		// then relocated by JS to sit right under the core "Profile Picture" row.
		?>
		<table class="form-table unysonplus-local-avatar-wrap" role="presentation" style="display:none;">
			<tbody>
			<tr class="unysonplus-local-avatar-row">
				<th><label><?php esc_html_e( 'Profile Photo', 'unysonplus' ); ?></label></th>
				<td>
					<div class="unysonplus-local-avatar" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
						<img
							class="unysonplus-local-avatar-preview"
							src="<?php echo esc_url( $url ); ?>"
							alt=""
							style="width:96px;height:96px;border-radius:50%;object-fit:cover;border:1px solid #c3c4c7;background:#f0f0f1;<?php echo $url ? '' : 'display:none;'; ?>"
						/>
						<input type="hidden" name="unysonplus_local_avatar" value="<?php echo esc_attr( $att_id ); ?>" />
						<span>
							<button type="button" class="button unysonplus-local-avatar-upload"><?php esc_html_e( 'Choose / Upload Image', 'unysonplus' ); ?></button>
							<button type="button" class="button unysonplus-local-avatar-remove" style="<?php echo $url ? '' : 'display:none;'; ?>"><?php esc_html_e( 'Remove', 'unysonplus' ); ?></button>
						</span>
						<?php wp_nonce_field( 'unysonplus_local_avatar_save', 'unysonplus_local_avatar_nonce' ); ?>
					</div>
					<p class="description"><?php esc_html_e( 'Upload an image to use as your avatar. When set, it replaces Gravatar everywhere on the site. Leave empty to fall back to Gravatar.', 'unysonplus' ); ?></p>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Persist the uploaded avatar on profile save.
	 */
	function unysonplus_local_avatar_save( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}
		if ( ! isset( $_POST['unysonplus_local_avatar_nonce'] )
			|| ! wp_verify_nonce( sanitize_key( $_POST['unysonplus_local_avatar_nonce'] ), 'unysonplus_local_avatar_save' ) ) {
			return;
		}
		$att_id = isset( $_POST['unysonplus_local_avatar'] ) ? absint( $_POST['unysonplus_local_avatar'] ) : 0;
		if ( $att_id ) {
			update_user_meta( $user_id, UNYSONPLUS_LOCAL_AVATAR_META, $att_id );
		} else {
			delete_user_meta( $user_id, UNYSONPLUS_LOCAL_AVATAR_META );
		}
	}

	/**
	 * Media library + picker JS, only on the profile / user-edit screens.
	 */
	function unysonplus_local_avatar_enqueue( $hook ) {
		if ( 'profile.php' !== $hook && 'user-edit.php' !== $hook ) {
			return;
		}
		wp_enqueue_media();
		$js = <<<'JS'
jQuery(function($){
	// Move the field directly beneath the core "Profile Picture" row.
	var $row = $('.unysonplus-local-avatar-row');
	var $picRow = $('tr.user-profile-picture');
	if ($row.length && $picRow.length) {
		$picRow.after($row);
		$('.unysonplus-local-avatar-wrap').remove();
	} else {
		$('.unysonplus-local-avatar-wrap').show();
	}

	var frame;
	var $wrap = $('.unysonplus-local-avatar');
	$wrap.on('click', '.unysonplus-local-avatar-upload', function(e){
		e.preventDefault();
		if (frame) { frame.open(); return; }
		frame = wp.media({
			title: 'Select or Upload Profile Photo',
			button: { text: 'Use this image' },
			library: { type: 'image' },
			multiple: false
		});
		frame.on('select', function(){
			var att = frame.state().get('selection').first().toJSON();
			var url = (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url;
			$wrap.find('.unysonplus-local-avatar-preview').attr('src', url).show();
			$wrap.find('input[name="unysonplus_local_avatar"]').val(att.id);
			$wrap.find('.unysonplus-local-avatar-remove').show();
		});
		frame.open();
	});
	$wrap.on('click', '.unysonplus-local-avatar-remove', function(e){
		e.preventDefault();
		$wrap.find('input[name="unysonplus_local_avatar"]').val('');
		$wrap.find('.unysonplus-local-avatar-preview').attr('src', '').hide();
		$(this).hide();
	});
});
JS;
		wp_add_inline_script( 'jquery-core', $js );
	}

	/** Register hooks. */
	function unysonplus_local_avatar_boot() {
		add_filter( 'get_avatar_data', 'unysonplus_local_avatar_data', 10, 2 );
		add_action( 'show_user_profile', 'unysonplus_local_avatar_field' );
		add_action( 'edit_user_profile', 'unysonplus_local_avatar_field' );
		add_action( 'personal_options_update', 'unysonplus_local_avatar_save' );
		add_action( 'edit_user_profile_update', 'unysonplus_local_avatar_save' );
		add_action( 'admin_enqueue_scripts', 'unysonplus_local_avatar_enqueue' );
	}

	unysonplus_local_avatar_boot();

endif;
