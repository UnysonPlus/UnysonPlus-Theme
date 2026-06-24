<?php $footer_copyright = fw_get_db_settings_option('footer_copyright');
                $menu_atts = fw_get_db_settings_option('footer_menu');
?>
<?php if( empty($footer_copyright['text']) && !has_nav_menu( 'footer' ) ) return; ?>
<div class="copyright-info">
        <div class="fw-container">
                <div class="fw-row">
                        <div class="fw-col-md-12">
                                <div class="float-lg-start"><?php echo $footer_copyright['text']; ?></div>
                                <?php if ( has_nav_menu( 'footer' ) ) : ?>
                                        <?php wp_nav_menu( array( 
                                        'menu'           => 'footer',
                                        'theme_location' => 'footer',
                                        'item_spacing'   => 'discard',
                                        'menu_class'             => 'nav float-lg-end list-inline') ); ?>
                                <?php endif; ?>

                                <?php if(!empty($menu_atts['social_profiles'])): ?>
                                        <ul class="social-profiles">
                                        <?php
                                        $social_profiles = fw_get_db_settings_option('social_profiles');
                                        for($i=0; $i < count($social_profiles); $i++): 
                                                if(!empty($social_profiles[$i]['link'])):
                                                        echo '<li><a href="'. $social_profiles[$i]['link'].'" target="_blank"><i class="fa '. $social_profiles[$i]['fa_code'].'" aria-hidden="true"></i></a></li>';
                                                endif;
                                        endfor; 
                                        ?>
                                        </ul><br>
                                <?php endif; ?>
                        </div>
                </div>
        </div>
</div><!-- .copyright-info -->

