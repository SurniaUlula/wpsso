<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2021 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoStdAdminAdvanced' ) ) {

	class WpssoStdAdminAdvanced {

		private $p;	// Wpsso class object.

		private $head_tags_opts = array();

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			$this->p->util->add_plugin_filters( $this, array(
				'plugin_interface_rows'         => 2,	// Advanced Settings > Interface tab.
				'plugin_integration_rows'       => 2,	// Advanced Settings > Integration tab.
				'plugin_cache_rows'             => 3,	// Advanced Settings > Caching tab.
				'services_media_rows'           => 2,	// Service APIs > Media Services tab.
				'services_shortening_rows'      => 2,	// Service APIs > Shortening Services tab.
				'services_ratings_reviews_rows' => 2,	// Service APIs > Ratings and Reviews tab.
				'cm_custom_contacts_rows'       => 2,	// Contact Fields > Custom Contacts tab.
				'cm_default_contacts_rows'      => 2,	// Contact Fields > Default Contacts tab.
				'metadata_product_attrs_rows'   => 2,	// Metadata > Product Attributes tab.
				'metadata_custom_fields_rows'   => 2,	// Metadata > Custom Fields tab.
				'head_tags_facebook_rows'       => 3,	// HTML Tags > Facebook tab.
				'head_tags_open_graph_rows'     => 3,	// HTML Tags > Open Graph tab.
				'head_tags_twitter_rows'        => 3,	// HTML Tags > Twitter tab.
				'head_tags_schema_rows'         => 3,	// HTML Tags > Schema tab.
				'head_tags_seo_other_rows'      => 3,	// HTML Tags > SEO / Other tab.
			), $prio = 20 );
		}

		/**
		 * Advanced Settings > Interface tab.
		 */
		public function filter_plugin_interface_rows( $table_rows, $form ) {

			$table_rows[] = '<td colspan="2">' . $this->p->msgs->pro_feature( 'wpsso' ) . '</td>';

			$table_rows[ 'plugin_show_opts' ] = '' .
				$form->get_th_html( _x( 'Plugin Options to Show by Default', 'option label', 'wpsso' ), '', 'plugin_show_opts' ) .
				'<td class="blank">' . $form->get_no_select( 'plugin_show_opts', $this->p->cf[ 'form' ][ 'show_options' ] ) . '</td>';

			$menu_title = _x( 'Validators', 'toolbar menu title', 'wpsso' );

			$table_rows[ 'plugin_show_validate_toolbar' ] = '' .	// Show Validators Toolbar Menu.
				$form->get_th_html( sprintf( _x( 'Show %s Toolbar Menu', 'option label', 'wpsso' ), $menu_title ),
					$css_class = '', $css_id = 'plugin_show_validate_toolbar' ) .
				$form->get_no_td_checkbox( 'plugin_show_validate_toolbar' );

			/**
			 * Show custom meta metaboxes.
			 */
			$add_to_metabox_title = _x( $this->p->cf[ 'meta' ][ 'title' ], 'metabox title', 'wpsso' );

			$add_to_values = array( 'user_page' => _x( 'User Profile', 'option label', 'wpsso' ) );
			$add_to_values = SucomUtilWP::get_post_type_labels( $add_to_values, $val_prefix = '', _x( 'Post Type', 'option label', 'wpsso' ) );
			$add_to_values = SucomUtilWP::get_taxonomy_labels( $add_to_values, $val_prefix = 'tax_', _x( 'Taxonomy', 'option label', 'wpsso' ) );

			$table_rows[ 'plugin_add_to' ] = '' .	// Show Document SSO Metabox.
				$form->get_th_html( sprintf( _x( 'Show %s Metabox', 'option label', 'wpsso' ), $add_to_metabox_title ),
					$css_class = '', $css_id = 'plugin_add_to' ) . 
				'<td class="blank">' . $form->get_no_checklist( 'plugin_add_to', $add_to_values,
					$css_class = 'input_vertical_list', $css_id = '', $is_assoc = true ) . '</td>';

			/**
			 * Additional item list columns.
			 */
			$list_cols = '<table class="plugin-list-columns">' . "\n" . '<tr>';

			foreach ( WpssoWpMeta::get_column_headers() as $col_key => $col_header ) {

				$list_cols .= '<th>' . $col_header . '</th>';
			}

			$list_cols .= '<td class="underline"></td></tr>' . "\n";

			foreach ( array(
				'post'  => __( 'Posts, Pages, and Custom Post Types', 'wpsso' ),
				'media' => __( 'Media Library', 'wpsso' ),
				'term'  => __( 'Categories, Tags, and Custom Taxonomies', 'wpsso' ),
				// translators: Please ignore - translation uses a different text domain.
				'user'  => __( 'Users' ),
			) as $mod_name => $mod_label ) {

				$list_cols .= '<tr>';

				foreach ( WpssoWpMeta::get_column_headers() as $col_key => $col_header ) {

					$opt_key = 'plugin_' . $col_key . '_col_' . $mod_name;

					if ( $form->in_defaults( $opt_key ) ) {	// Just in case.

						$list_cols .= $form->get_no_td_checkbox( $opt_key, $comment = '', $extra_css_class = 'checkbox' );	// Narrow column.

					} else {

						$list_cols .= '<td class="checkbox"></td>';
					}
				}

				$list_cols .= '<td class="blank"><p>' . $mod_label . '</p></td></tr>' . "\n";
			}

			$list_cols .= '</table>' . "\n";

			$table_rows[ 'plugin_show_columns' ] = '' .
				$form->get_th_html( _x( 'Additional Item List Columns', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_show_columns' ) .
				'<td>' . $list_cols . '</td>';

			/**
			 * Default and custom column widths.
			 */
			$table_rows[ 'plugin_col_title_width' ] = $form->get_tr_hide( 'basic', 'plugin_col_title_width' ) . 
				$form->get_th_html( _x( 'Title / Name Column Width', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_col_title_width' ) . 
				'<td>' . $form->get_no_input( 'plugin_col_title_width', 'short' ) . ' ' .
				_x( 'and max width', 'option comment', 'wpsso' ) . ' ' . $form->get_no_input( 'plugin_col_title_width_max', 'short' ) . '</td>';

			$table_rows[ 'plugin_col_def_width' ] = $form->get_tr_hide( 'basic', 'plugin_col_def_width' ) . 
				$form->get_th_html( _x( 'Default for Posts / Pages List', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_col_def_width' ) . 
				'<td>' . $form->get_no_input( 'plugin_col_def_width', 'short' ) .
				_x( 'and max width', 'option comment', 'wpsso' ) . ' ' . $form->get_no_input( 'plugin_col_def_width_max', 'short' ) . '</td>';

			return $table_rows;
		}

		/**
		 * Advanced Settings > Integration tab.
		 */
		public function filter_plugin_integration_rows( $table_rows, $form ) {

			$table_rows[] = '<td colspan="2">' . $this->p->msgs->pro_feature( 'wpsso' ) . '</td>';

			$table_rows[ 'plugin_document_title' ] = '' .
				$form->get_th_html( _x( 'Webpage Document Title', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_document_title' ) .
				'<td class="blank">' . $form->get_no_select( 'plugin_document_title',  $this->p->cf[ 'form' ][ 'document_title' ] ) .
					$this->p->msgs->maybe_title_tag_disabled() . '</td>';

			$table_rows[ 'plugin_filter_title' ] = '' . 
				$form->get_th_html( _x( 'Use WordPress Title Filters', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_filter_title' ) . 
				$form->get_no_td_checkbox( 'plugin_filter_title',
					'<em>' . _x( 'not recommended', 'option comment', 'wpsso' ) . '</em>' );

			$table_rows[ 'plugin_filter_content' ] = '' . 
				$form->get_th_html( _x( 'Use WordPress Content Filters', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_filter_content' ) . 
				$form->get_no_td_checkbox( 'plugin_filter_content',
					'<em>' . _x( 'recommended (see help text)', 'option comment', 'wpsso' ) . '</em>' );

			$table_rows[ 'plugin_filter_excerpt' ] = '' . 
				$form->get_th_html( _x( 'Use WordPress Excerpt Filters', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_filter_excerpt' ) . 
				$form->get_no_td_checkbox( 'plugin_filter_excerpt',
					'<em>' . _x( 'recommended only if using shortcodes in excerpts', 'option comment', 'wpsso' ) . '</em>' );

			$table_rows[ 'plugin_p_strip' ] = $form->get_tr_hide( 'basic', 'plugin_p_strip' ) .
				$form->get_th_html( _x( 'Content Starts at 1st Paragraph', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_p_strip' ) . 
				$form->get_no_td_checkbox( 'plugin_p_strip' );

			$table_rows[ 'plugin_use_img_alt' ] = $form->get_tr_hide( 'basic', 'plugin_use_img_alt' ) .
				$form->get_th_html( _x( 'Use Image Alt if No Content', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_use_img_alt' ) . 
				$form->get_no_td_checkbox( 'plugin_use_img_alt' );

			$table_rows[ 'plugin_img_alt_prefix' ] = '' . 
				$form->get_th_html_locale( _x( 'Content Image Alt Prefix', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_img_alt_prefix' ) . 
				'<td class="blank">' . SucomUtil::get_key_value( 'plugin_img_alt_prefix', $form->options ) . '</td>';

			$table_rows[ 'plugin_p_cap_prefix' ] = '' . 
				$form->get_th_html_locale( _x( 'WP Caption Text Prefix', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_p_cap_prefix' ) . 
				'<td class="blank">' . SucomUtil::get_key_value( 'plugin_p_cap_prefix', $form->options ) . '</td>';

			$table_rows[ 'plugin_no_title_text' ] = '' . 
				$form->get_th_html_locale( _x( 'No Title Text', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_no_title_text' ) . 
				'<td class="blank">' . SucomUtil::get_key_value( 'plugin_no_title_text', $form->options ) . '</td>';

			$table_rows[ 'plugin_no_desc_text' ] = '' . 
				$form->get_th_html_locale( _x( 'No Description Text', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_no_desc_text' ) . 
				'<td class="blank">' . SucomUtil::get_key_value( 'plugin_no_desc_text', $form->options ) . '</td>';

			$table_rows[ 'plugin_page_excerpt' ] = '' . 
				$form->get_th_html( _x( 'Enable WP Excerpt for Pages', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_page_excerpt' ) . 
				$form->get_no_td_checkbox( 'plugin_page_excerpt' );

			$table_rows[ 'plugin_page_tags' ] = $form->get_tr_hide( 'basic', 'plugin_page_tags' ) .
				$form->get_th_html( _x( 'Enable WP Tags for Pages', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_page_tags' ) . 
				$form->get_no_td_checkbox( 'plugin_page_tags' );

			$table_rows[ 'plugin_new_user_is_person' ] = '' . 
				$form->get_th_html( _x( 'Add Person Role for New Users', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_new_user_is_person' ) . 
				$form->get_no_td_checkbox( 'plugin_new_user_is_person' );

			$table_rows[ 'plugin_check_head' ] = $form->get_tr_hide( 'basic', 'plugin_check_head' ) .
				$form->get_th_html( _x( 'Check for Duplicate Meta Tags', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_check_head' ) . 
				$form->get_no_td_checkbox( 'plugin_check_head' );

			$table_rows[ 'plugin_check_img_dims' ] = '' . 
				$form->get_th_html( _x( 'Enforce Image Dimension Checks', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_check_img_dims' ) . 
				$form->get_no_td_checkbox( 'plugin_check_img_dims', '<em>' . _x( 'recommended', 'option comment', 'wpsso' ) . '</em>' );

			$table_rows[ 'plugin_upscale_images' ] = '' . 
				$form->get_th_html( _x( 'Upscale Media Library Images', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_upscale_images' ) . 
				$form->get_no_td_checkbox( 'plugin_upscale_images' );

			$table_rows[ 'plugin_upscale_img_max' ] = $form->get_tr_hide( 'basic', 'plugin_upscale_img_max' ) .
				$form->get_th_html( _x( 'Maximum Image Upscale Percent', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_upscale_img_max' ) . 
				'<td class="blank">' . $form->get_no_input( 'plugin_upscale_img_max', $css_class = 'short' ) . ' %</td>';

			/**
			 * Read Yoast SEO social meta.
			 */
			$table_rows[ 'plugin_wpseo_social_meta' ] = '' .
				$form->get_th_html( _x( 'Import Yoast SEO Social Meta', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_wpseo_social_meta' ) . 
				$form->get_no_td_checkbox( 'plugin_wpseo_social_meta' );

			$table_rows[ 'plugin_wpseo_show_import' ] = $form->get_tr_hide( 'basic', 'plugin_wpseo_show_import' ) .
				$form->get_th_html( _x( 'Show Yoast SEO Import Details', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_wpseo_show_import' ) .
				$form->get_no_td_checkbox( 'plugin_wpseo_show_import' );

			return $table_rows;
		}

		/**
		 * Advanced Settings > Caching tab.
		 */
		public function filter_plugin_cache_rows( $table_rows, $form, $network = false ) {

			$table_rows[] = '<td colspan="' . ( $network ? 4 : 2 ) . '">' . $this->p->msgs->pro_feature( 'wpsso' ) . '</td>';

			$table_rows[ 'plugin_head_cache_exp' ] = '' . 
				$form->get_th_html( _x( 'Head Markup Cache Expiry', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_head_cache_exp' ) . 
				'<td nowrap class="blank">' . $form->get_no_input( 'plugin_head_cache_exp', $css_class = 'medium' ) . ' ' . 
				_x( 'seconds (0 to disable)', 'option comment', 'wpsso' ) . '</td>' . 
				WpssoAdmin::get_option_site_use( 'plugin_head_cache_exp', $form, $network );

			$table_rows[ 'plugin_content_cache_exp' ] = $form->get_tr_hide( 'basic', 'plugin_content_cache_exp' ) . 
				$form->get_th_html( _x( 'Filtered Content Cache Expiry', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_content_cache_exp' ) . 
				'<td nowrap class="blank">' . $form->get_no_input( 'plugin_content_cache_exp', $css_class = 'medium' ) . ' ' . 
				_x( 'seconds (0 to disable)', 'option comment', 'wpsso' ) . '</td>' . 
				WpssoAdmin::get_option_site_use( 'plugin_content_cache_exp', $form, $network );

			$table_rows[ 'plugin_imgsize_cache_exp' ] = '' .
				$form->get_th_html( _x( 'Image URL Info Cache Expiry', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_imgsize_cache_exp' ) . 
				'<td nowrap class="blank">' . $form->get_no_input( 'plugin_imgsize_cache_exp', $css_class = 'medium' ) . ' ' . 
				_x( 'seconds (0 to disable)', 'option comment', 'wpsso' ) . '</td>' . 
				WpssoAdmin::get_option_site_use( 'plugin_imgsize_cache_exp', $form, $network );

			$table_rows[ 'plugin_vidinfo_cache_exp' ] = '' .
				$form->get_th_html( _x( 'Video API Info Cache Expiry', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_vidinfo_cache_exp' ) . 
				'<td nowrap class="blank">' . $form->get_no_input( 'plugin_vidinfo_cache_exp', $css_class = 'medium' ) . ' ' . 
				_x( 'seconds (0 to disable)', 'option comment', 'wpsso' ) . '</td>' . 
				WpssoAdmin::get_option_site_use( 'plugin_vidinfo_cache_exp', $form, $network );

			$table_rows[ 'plugin_short_url_cache_exp' ] = '' .
				$form->get_th_html( _x( 'Shortened URL Cache Expiry', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_short_url_cache_exp' ) . 
				'<td nowrap class="blank">' . $form->get_no_input( 'plugin_short_url_cache_exp', $css_class = 'medium' ) . ' ' . 
				_x( 'seconds (0 to disable)', 'option comment', 'wpsso' ) . '</td>' . 
				WpssoAdmin::get_option_site_use( 'plugin_short_url_cache_exp', $form, $network );

			$table_rows[ 'plugin_types_cache_exp' ] = $form->get_tr_hide( 'basic', 'plugin_types_cache_exp' ) . 
				$form->get_th_html( _x( 'Schema Index Cache Expiry', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_types_cache_exp' ) . 
				'<td nowrap class="blank">' . $form->get_no_input( 'plugin_types_cache_exp', $css_class = 'medium' ) . ' ' . 
				_x( 'seconds (0 to disable)', 'option comment', 'wpsso' ) . '</td>' . 
				WpssoAdmin::get_option_site_use( 'plugin_types_cache_exp', $form, $network );

			$table_rows[ 'plugin_select_cache_exp' ] = $form->get_tr_hide( 'basic', 'plugin_select_cache_exp' ) . 
				$form->get_th_html( _x( 'Form Selects Cache Expiry', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_select_cache_exp' ) .
				'<td nowrap class="blank">' . $form->get_no_input( 'plugin_select_cache_exp', $css_class = 'medium' ) . ' ' . 
				_x( 'seconds (0 to disable)', 'option comment', 'wpsso' ) . '</td>' . 
				WpssoAdmin::get_option_site_use( 'plugin_select_cache_exp', $form, $network );

			$table_rows[ 'plugin_clear_on_activate' ] = $form->get_tr_hide( 'basic', 'plugin_clear_on_activate' ) . 
				$form->get_th_html( _x( 'Clear All Caches on Activate', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_clear_on_activate' ) . 
				$form->get_no_td_checkbox( 'plugin_clear_on_activate' ) . 
				WpssoAdmin::get_option_site_use( 'plugin_clear_on_activate', $form, $network );

			$table_rows[ 'plugin_clear_on_deactivate' ] = $form->get_tr_hide( 'basic', 'plugin_clear_on_deactivate' ) . 
				$form->get_th_html( _x( 'Clear All Caches on Deactivate', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_clear_on_deactivate' ) . 
				$form->get_no_td_checkbox( 'plugin_clear_on_deactivate' ) . 
				WpssoAdmin::get_option_site_use( 'plugin_clear_on_deactivate', $form, $network );

			$table_rows[ 'plugin_clear_short_urls' ] = $form->get_tr_hide( 'basic', 'plugin_clear_short_urls' ) . 
				$form->get_th_html( _x( 'Refresh Short URLs on Clear Cache', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_clear_short_urls' ) . 
				$form->get_no_td_checkbox( 'plugin_clear_short_urls' ) . 
				WpssoAdmin::get_option_site_use( 'plugin_clear_short_urls', $form, $network );

			$table_rows[ 'plugin_clear_post_terms' ] = $form->get_tr_hide( 'basic', 'plugin_clear_post_terms' ) . 
				$form->get_th_html( _x( 'Clear Term Cache for Published Post', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_clear_post_terms' ) . 
				$form->get_no_td_checkbox( 'plugin_clear_post_terms' ) . 
				WpssoAdmin::get_option_site_use( 'plugin_clear_post_terms', $form, $network );

			$table_rows[ 'plugin_clear_for_comment' ] = $form->get_tr_hide( 'basic', 'plugin_clear_for_comment' ) . 
				$form->get_th_html( _x( 'Clear Post Cache for New Comment', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_clear_for_comment' ) . 
				$form->get_no_td_checkbox( 'plugin_clear_for_comment' ) . 
				WpssoAdmin::get_option_site_use( 'plugin_clear_for_comment', $form, $network );

			return $table_rows;
		}

		/**
		 * Service APIs > Media Services tab.
		 */
		public function filter_services_media_rows( $table_rows, $form ) {

			$table_rows[] = '<td colspan="2">' . $this->p->msgs->pro_feature( 'wpsso' ) . '</td>';

			$table_rows[ 'plugin_gravatar_api' ] = '' . 
				$form->get_th_html( _x( 'Gravatar is Default Author Image', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_gravatar_api' ) . 
				$form->get_no_td_checkbox( 'plugin_gravatar_api' );

			$table_rows[ 'plugin_gravatar_size' ] = $form->get_tr_hide( 'basic', 'plugin_gravatar_size' ) . 
				$form->get_th_html( _x( 'Gravatar Image Size', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_gravatar_size' ) . 
				'<td class="blank">' . $form->get_no_input( 'plugin_gravatar_size', $css_class = 'short' ) . '</td>';

			$check_embed_html = '';

			foreach ( $this->p->cf[ 'form' ][ 'embed_media_apis' ] as $opt_key => $opt_label ) {

				$check_embed_html .= '<p>' . $form->get_no_checkbox_comment( $opt_key ) . ' ' . _x( $opt_label, 'option value', 'wpsso' ) . '</p>';
			}

			$table_rows[ 'plugin_embed_media_apis' ] = '' .
				$form->get_th_html( _x( 'Check for Embedded Media', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_embed_media_apis' ).
				'<td class="blank">' . $check_embed_html . '</td>';

			return $table_rows;
		}

		/**
		 * Service APIs > Shortening Services tab.
		 */
		public function filter_services_shortening_rows( $table_rows, $form ) {

			$table_rows[] = '<td colspan="2">' . $this->p->msgs->pro_feature( 'wpsso' ) . '</td>';

			$table_rows[ 'plugin_shortener' ] = '' . 
				$form->get_th_html( _x( 'URL Shortening Service', 'option label', 'wpsso' ), $css_class = '', $css_id = 'plugin_shortener' ) . 
				'<td class="blank">' . $form->get_no_select_none( 'plugin_shortener' ) . '</td>';

			$table_rows[ 'plugin_min_shorten' ] = $form->get_tr_hide( 'basic', 'plugin_min_shorten' ) . 
				$form->get_th_html( _x( 'Minimum URL Length to Shorten', 'option label', 'wpsso' ), $css_class = '', $css_id = 'plugin_min_shorten' ) . 
				'<td nowrap class="blank">' . $form->get_no_input( 'plugin_min_shorten', $css_class = 'short' ) . ' ' .
					_x( 'characters', 'option comment', 'wpsso' ) . '</td>';

			$table_rows[ 'plugin_wp_shortlink' ] = $form->get_tr_hide( 'basic', 'plugin_wp_shortlink' ) .
				$form->get_th_html( _x( 'Use Shortened URL for WP Shortlink', 'option label', 'wpsso' ), $css_class = '', $css_id = 'plugin_wp_shortlink' ) . 
				$form->get_no_td_checkbox( 'plugin_wp_shortlink' );

			$table_rows[ 'plugin_add_link_rel_shortlink' ] = $form->get_tr_hide( 'basic', 'add_link_rel_shortlink' ) .
				$form->get_th_html( sprintf( _x( 'Add "%s" HTML Tag', 'option label', 'wpsso' ), 'link&nbsp;rel&nbsp;shortlink' ),
					$css_class = '', $css_id = 'plugin_add_link_rel_shortlink' ) . 
				'<td class="blank">' . $form->get_no_checkbox( 'add_link_rel_shortlink', $css_class = '', $css_id = 'add_link_rel_shortlink_html_tag',
					$force = null, $group = 'add_link_rel_shortlink' ) . '</td>';	// Group with option in head tags list

			return $table_rows;
		}

		/**
		 * Service APIs > Ratings and Reviews tab.
		 */
		public function filter_services_ratings_reviews_rows( $table_rows, $form ) {

			$table_rows[] = '<td colspan="2">' . $this->p->msgs->pro_feature( 'wpsso' ) . '</td>';

			/**
			 * Shopper Approved.
			 */
			$table_rows[ 'subsection_plugin_shopperapproved' ] = '' .
				'<td colspan="2" class="subsection top"><h4>' . _x( 'Shopper Approved', 'metabox title', 'wpsso' ) . '</h4></td>';

			$table_rows[ 'plugin_shopperapproved_site_id' ] = '' .
				$form->get_th_html( _x( 'Shopper Approved Site ID', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_shopperapproved_site_id' ) .
				'<td class="blank">' . $form->get_no_input( 'plugin_shopperapproved_site_id', $css_class = 'api_key' ) . '</td>';

			$table_rows[ 'plugin_shopperapproved_token' ] = '' .
				$form->get_th_html( _x( 'Shopper Approved API Token', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_shopperapproved_token' ) .
				'<td class="blank">' . $form->get_no_input( 'plugin_shopperapproved_token', $css_class = 'api_key' ) . '</td>';

			$table_rows[ 'plugin_shopperapproved_num_max' ] = '' .
				$form->get_th_html( _x( 'Maximum Number of Reviews', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_shopperapproved_num_max' ) .
				'<td class="blank">' . $form->get_no_input( 'plugin_shopperapproved_num_max', $css_class = 'short' ) . '</td>';

			$table_rows[ 'plugin_shopperapproved_age_max' ] = '' .
				$form->get_th_html( _x( 'Maximum Age of Reviews', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_shopperapproved_age_max' ) .
				'<td nowrap class="blank">' . $form->get_no_input( 'plugin_shopperapproved_age_max', $css_class = 'short' ) . ' ' .
					_x( 'months', 'option comment', 'wpsso' ) . '</td>';

			$sa_for_values = SucomUtilWP::get_post_type_labels( array(), $val_prefix = '', _x( 'Post Type', 'option label', 'wpsso' ) );

			$table_rows[ 'plugin_shopperapproved_for' ] = '' .
				$form->get_th_html( _x( 'Get Reviews for Post Types', 'option label', 'wpsso' ),
					$css_class = '', $css_id = 'plugin_shopperapproved_for' ) .
				'<td class="blank">' . $form->get_no_checklist( 'plugin_shopperapproved_for', $sa_for_values,
					$css_class = 'input_vertical_list', $css_id = '', $is_assoc = true ) . '</td>';

			return $table_rows;
		}

		/**
		 * Contact Fields > Custom Contacts tab.
		 */
		public function filter_cm_custom_contacts_rows( $table_rows, $form ) {

			$table_rows[] = '<td colspan="4">' . $this->p->msgs->pro_feature( 'wpsso' ) . '</td>';

			$table_rows[] = '<td></td>' . 
				$form->get_th_html( _x( 'Show', 'column title', 'wpsso' ),
					$css_class = 'checkbox left', 'custom-cm-show-checkbox' ) . 
				$form->get_th_html( _x( 'Contact Field ID', 'column title', 'wpsso' ),
					$css_class = 'medium left', 'custom-cm-field-id' ) . 
				$form->get_th_html_locale( _x( 'Contact Field Label', 'column title', 'wpsso' ),
					$css_class = 'wide left', 'custom-cm-field-label' );

			$sorted_opt_pre = $this->p->cf[ 'opt' ][ 'cm_prefix' ];

			ksort( $sorted_opt_pre );

			foreach ( $sorted_opt_pre as $cm_id => $opt_pre ) {

				$cm_enabled_key = 'plugin_cm_' . $opt_pre . '_enabled';
				$cm_name_key    = 'plugin_cm_' . $opt_pre . '_name';
				$cm_label_key   = 'plugin_cm_' . $opt_pre . '_label';

				/**
				 * Not all social sites have a contact method field.
				 */
				if ( ! isset( $form->options[ $cm_enabled_key ] ) ) {

					continue;
				}

				/**
				 * Additional labels may be defined by social sharing add-ons.
				 */
				$opt_label = empty( $this->p->cf[ '*' ][ 'lib' ][ 'share' ][ $cm_id ] ) ?
					ucfirst( $cm_id ) : $this->p->cf[ '*' ][ 'lib' ][ 'share' ][ $cm_id ];

				/**
				 * Hide by default if the contact method is not enabled by default.
				 */
				$tr_html = empty( $form->defaults[ $cm_enabled_key ] ) ?
					$form->get_tr_hide( 'basic', array( $cm_enabled_key, $cm_name_key, $cm_label_key ) ) : '';

				$table_rows[] = $tr_html .
					$form->get_th_html( $opt_label, $css_class = 'medium' ) . 
					$form->get_no_td_checkbox( $cm_enabled_key, $comment = '', $extra_css_class = 'checkbox' ) . 
					'<td class="blank medium">' . $form->get_no_input( $cm_name_key, $css_class = 'medium' ) . '</td>' . 
					'<td class="blank wide">' . $form->get_no_input_locale( $cm_label_key ) . '</td>';
			}

			return $table_rows;
		}

		/**
		 * Contact Fields > Default Contacts tab.
		 */
		public function filter_cm_default_contacts_rows( $table_rows, $form ) {

			$table_rows[] = '<td colspan="4">' . $this->p->msgs->pro_feature( 'wpsso' ) . '</td>';

			$table_rows[] = '<td></td>' . 
				$form->get_th_html( _x( 'Show', 'column title', 'wpsso' ),
					$css_class = 'checkbox left', 'custom-cm-show-checkbox' ) . 
				$form->get_th_html( _x( 'Contact Field ID', 'column title', 'wpsso' ),
					$css_class = 'medium left', 'wp-cm-field-id' ) . 
				$form->get_th_html_locale( _x( 'Contact Field Label', 'column title', 'wpsso' ),
					$css_class = 'wide left', 'custom-cm-field-label' );

			$sorted_cm_names = $this->p->cf[ 'wp' ][ 'cm_names' ];

			ksort( $sorted_cm_names );

			foreach ( $sorted_cm_names as $cm_id => $opt_label ) {

				$cm_enabled_key = 'wp_cm_' . $cm_id . '_enabled';
				$cm_name_key    = 'wp_cm_' . $cm_id . '_name';
				$cm_label_key   = 'wp_cm_' . $cm_id . '_label';

				/**
				 * Not all social websites have a contact method field.
				 */
				if ( ! isset( $form->options[ $cm_enabled_key ] ) ) {

					continue;
				}

				$table_rows[] = $form->get_th_html( $opt_label, $css_class = 'medium' ) . 
					$form->get_no_td_checkbox( $cm_enabled_key, $comment = '', $extra_css_class = 'checkbox' ) . 
					'<td class="medium">' . $form->get_no_input( $cm_name_key, $css_class = 'medium' ) . '</td>' . 
					'<td class="blank wide">' . $form->get_no_input_locale( $cm_label_key ) . '</td>';
			}

			return $table_rows;
		}

		/**
		 * Metadata > Product Attributes tab.
		 */
		public function filter_metadata_product_attrs_rows( $table_rows, $form ) {

			$table_rows[] = '<td colspan="2">' . $this->p->msgs->get( 'info-product-attrs' ) . '</td>';

			$table_rows[] = '<td colspan="2">' . $this->p->msgs->pro_feature( 'wpsso' ) . '</td>';

			foreach ( $this->p->cf[ 'form' ][ 'attr_labels' ] as $opt_key => $opt_label ) {

				$cmt_transl = WpssoAdmin::get_option_unit_comment( $opt_key );

				$table_rows[ $opt_key ] = '' .
					$form->get_th_html( _x( $opt_label, 'option label', 'wpsso' ), '', $opt_key ) . 
					'<td class="blank">' . $form->get_no_input( $opt_key ) . $cmt_transl . '</td>';
			}
			return $table_rows;
		}

		/**
		 * Metadata > Custom Fields tab.
		 */
		public function filter_metadata_custom_fields_rows( $table_rows, $form ) {

			$table_rows[] = '<td colspan="2">' . $this->p->msgs->get( 'info-custom-fields' ) . '</td>';

			$table_rows[] = '<td colspan="2">' . $this->p->msgs->pro_feature( 'wpsso' ) . '</td>';

			/**
			 * Example config:
			 *
			 * 	$cf_md_index = array(
			 *		'plugin_cf_addl_type_urls'           => 'schema_addl_type_url',
			 *		'plugin_cf_howto_steps'              => 'schema_howto_step',
			 *		'plugin_cf_howto_supplies'           => 'schema_howto_supply',
			 *		'plugin_cf_howto_tools'              => 'schema_howto_tool',
			 *		'plugin_cf_img_url'                  => 'og_img_url',
			 *		'plugin_cf_product_avail'            => 'product_avail',
			 *		'plugin_cf_product_brand'            => 'product_brand',
			 *		'plugin_cf_product_color'            => 'product_color',
			 *		'plugin_cf_product_condition'        => 'product_condition',
			 *		'plugin_cf_product_currency'         => 'product_currency',
			 *		'plugin_cf_product_material'         => 'product_material',
			 *		'plugin_cf_product_mfr_part_no'      => 'product_mfr_part_no',		// Product MPN.
			 *		'plugin_cf_product_price'            => 'product_price',
			 *		'plugin_cf_product_retailer_part_no' => 'product_retailer_part_no',	// Product SKU.
			 *		'plugin_cf_product_size'             => 'product_size',
			 *		'plugin_cf_product_target_gender'    => 'product_target_gender',
			 *		'plugin_cf_recipe_ingredients'       => 'schema_recipe_ingredient',
			 *		'plugin_cf_recipe_instructions'      => 'schema_recipe_instruction',
			 *		'plugin_cf_sameas_urls'              => 'schema_sameas_url',
			 *		'plugin_cf_vid_embed'                => 'og_vid_embed',
			 *		'plugin_cf_vid_url'                  => 'og_vid_url',
			 * 	);
			 *
			 * Hooked by the WpssoProRecipeWpRecipeMaker and WpssoProRecipeWpUltimateRecipe classes
			 * to clear the 'plugin_cf_recipe_ingredients' and 'plugin_cf_recipe_instructions' values.
			 */
			$cf_md_index = (array) apply_filters( 'wpsso_cf_md_index', $this->p->cf[ 'opt' ][ 'cf_md_index' ] );

			$opt_labels = array();

			foreach ( $cf_md_index as $opt_key => $md_key ) {

				/**
				 * Make sure we have a label for the custom field option.
				 */
				if ( ! empty( $this->p->cf[ 'form' ][ 'cf_labels' ][ $opt_key ] ) ) {

					$opt_labels[ $opt_key ] = $this->p->cf[ 'form' ][ 'cf_labels' ][ $opt_key ];
				}
			}

			asort( $opt_labels );

			foreach ( $opt_labels as $opt_key => $opt_label ) {

				/**
				 * If we don't have a meta data key, then clear the custom field name (just in case) and disable
				 * the option.
				 */
				if ( empty( $cf_md_index[ $opt_key ] ) ) {

					$form->options[ $opt_key ] = '';

					$always_disabled = true;

				} else {
					$always_disabled = false;
				}

				$cmt_transl = WpssoAdmin::get_option_unit_comment( $opt_key );

				$table_rows[ $opt_key ] = '' .
					$form->get_th_html( _x( $opt_label, 'option label', 'wpsso' ), '', $opt_key ) . 
					'<td class="blank">' . $form->get_no_input( $opt_key, $css_class = '', $css_id = '',
						$max_len = 0, $holder = '', $always_disabled ) . $cmt_transl . '</td>';
			}
			return $table_rows;
		}

		/**
		 * HTML Tags > Facebook tab.
		 */
		public function filter_head_tags_facebook_rows( $table_rows, $form, $network = false ) {

			return $this->get_head_tags_rows( $table_rows, $form, $network, array( '/^add_(meta)_(property)_((fb|al):.+)$/' ) );
		}

		/**
		 * HTML Tags > Open Graph tab.
		 */
		public function filter_head_tags_open_graph_rows( $table_rows, $form, $network = false ) {

			return $this->get_head_tags_rows( $table_rows, $form, $network, array( '/^add_(meta)_(property)_(.+)$/' ) );
		}

		/**
		 * HTML Tags > Twitter tab.
		 */
		public function filter_head_tags_twitter_rows( $table_rows, $form, $network = false ) {

			return $this->get_head_tags_rows( $table_rows, $form, $network, array( '/^add_(meta)_(name)_(twitter:.+)$/' ) );
		}

		/**
		 * HTML Tags > Schema tab.
		 */
		public function filter_head_tags_schema_rows( $table_rows, $form, $network = false ) {

			if ( empty( $this->p->avail[ 'p' ][ 'schema' ] ) ) {

				return $this->p->msgs->get_schema_disabled_rows( $table_rows );
			}

			return $this->get_head_tags_rows( $table_rows, $form, $network, array( '/^add_(meta|link)_(itemprop)_(.+)$/' ) );
		}

		/**
		 * HTML Tags > SEO / Other tab.
		 */
		public function filter_head_tags_seo_other_rows( $table_rows, $form, $network = false ) {

			if ( ! empty( $this->p->avail[ 'seo' ][ 'any' ] ) ) {

				$table_rows[] = '<td colspan="8"><blockquote class="top-info"><p>' . 
					__( 'An SEO plugin has been detected &mdash; some basic SEO meta tags have been unchecked and disabled automatically.', 'wpsso' ) . 
						'</p></blockquote></td>';
			}

			return $this->get_head_tags_rows( $table_rows, $form, $network, array( '/^add_(link)_([^_]+)_(.+)$/', '/^add_(meta)_(name)_(.+)$/' ) );
		}

		private function get_head_tags_rows( $table_rows, $form, $network, array $opt_preg_include ) {

			$table_cells = array();

			foreach ( $opt_preg_include as $preg ) {

				foreach ( $form->defaults as $opt_key => $opt_val ) {


					if ( strpos( $opt_key, 'add_' ) !== 0 ) {	// Optimize

						continue;

					} elseif ( isset( $this->head_tags_opts[ $opt_key ] ) ) {	// Check cache for tags already shown.

						continue;

					} elseif ( ! preg_match( $preg, $opt_key, $match ) ) {	// Check option name for a match.

						continue;
					}

					$highlight = '';
					$css_class = '';
					$css_id    = '';
					$force     = null;
					$group     = null;

					$this->head_tags_opts[ $opt_key ] = $opt_val;

					switch ( $opt_key ) {

						case 'add_meta_name_generator':	// Disabled with a constant instead.

							continue 2;

						case 'add_link_rel_shortlink':

							$group = 'add_link_rel_shortlink';

							break;
					}

					$table_cells[] = '<!-- ' . ( implode( ' ', $match ) ) . ' -->' . 	// Required for sorting.
						'<td class="checkbox blank">' . $form->get_no_checkbox( $opt_key, $css_class, $css_id, $force, $group ) . '</td>' . 
						'<td class="xshort' . $highlight . '">' . $match[1] . '</td>' . 
						'<td class="head_tags' . $highlight . '">' . $match[2] . '</td>' . 
						'<th class="head_tags' . $highlight . '">' . $match[3] . '</th>';
				}
			}

			return array_merge( $table_rows, SucomUtil::get_column_rows( $table_cells, 2 ) );
		}
	}
}
