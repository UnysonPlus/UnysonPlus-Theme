<?php $unique_id = esc_attr( uniqid( 'search-form-' ) ); ?>
<form role="search" method="get" class="form search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="input-group">
	  <input name="s" id="<?php echo $unique_id; ?>" type="text" class="search-field form-control" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'unysonplus' ); ?>" value="<?php echo get_search_query(); ?>" >
	  <span class="input-group-btn">
		<button type="submit" class="btn btn-danger" aria-label="<?php esc_attr_e( 'Search', 'unysonplus' ); ?>"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;</button>
	  </span>
	</div>
</form>