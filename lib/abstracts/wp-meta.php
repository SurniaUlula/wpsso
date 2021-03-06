<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2021 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! defined( 'WPSSO_PLUGINDIR' ) ) {

	die( 'Do. Or do not. There is no try.' );
}

/**
 * WordPress metadata class, extended by the WpssoPost, WpssoTerm, and WpssoUser classes.
 */
if ( ! class_exists( 'WpssoWpMeta' ) ) {

	abstract class WpssoWpMeta {

		protected $p;		// Wpsso class object.
		protected $form;	// SucomForm class object.

		protected $md_cache_disabled = false;	// Disable local caches when saving options.

		protected static $head_tags = false;	// Must be false by default.

		protected static $head_info = array();

		protected static $rename_keys_by_ext = array(
			'wpsso' => array(
				499 => array(
					'link_desc' => 'seo_desc',
					'meta_desc' => 'seo_desc',
				),
				503 => array(
					'schema_recipe_calories' => 'schema_recipe_nutri_cal',
				),
				514 => array(
					'rp_img_id'     => 'p_img_id',
					'rp_img_id_pre' => 'p_img_id_pre',
					'rp_img_width'  => '',
					'rp_img_height' => '',
					'rp_img_crop'   => '',
					'rp_img_crop_x' => '',
					'rp_img_crop_y' => '',
					'rp_img_url'    => 'p_img_url',
				),
				537 => array(
					'schema_add_type_url' => 'schema_addl_type_url_0',
				),
				569 => array(
					'schema_add_type_url' => 'schema_addl_type_url',	// Option modifiers are preserved.
				),
				615 => array(
					'org_type' => 'org_schema_type',
				),
				628 => array(
					'product_gender' => 'product_target_gender',
				),
				649 => array(
					'product_ean' => 'product_gtin13',
				),

				/**
				 * The custom width, height, and crop options were removed in preference for attachment specific
				 * options (ie. 'attach_img_crop_x' and 'attach_img_crop_y').
				 */
				660 => array(
					'og_img_width'              => '',
					'og_img_height'             => '',
					'og_img_crop'               => '',
					'og_img_crop_x'             => '',
					'og_img_crop_y'             => '',
					'schema_article_img_width'  => '',
					'schema_article_img_height' => '',
					'schema_article_img_crop'   => '',
					'schema_article_img_crop_x' => '',
					'schema_article_img_crop_y' => '',
					'schema_img_width'          => '',
					'schema_img_height'         => '',
					'schema_img_crop'           => '',
					'schema_img_crop_x'         => '',
					'schema_img_crop_y'         => '',
					'tc_sum_img_width'          => '',
					'tc_sum_img_height'         => '',
					'tc_sum_img_crop'           => '',
					'tc_sum_img_crop_x'         => '',
					'tc_sum_img_crop_y'         => '',
					'tc_lrg_img_width'          => '',
					'tc_lrg_img_height'         => '',
					'tc_lrg_img_crop'           => '',
					'tc_lrg_img_crop_x'         => '',
					'tc_lrg_img_crop_y'         => '',
					'thumb_img_width'           => '',
					'thumb_img_height'          => '',
					'thumb_img_crop'            => '',
					'thumb_img_crop_x'          => '',
					'thumb_img_crop_y'          => '',
				),
				692 => array(
					'product_mpn' => 'product_mfr_part_no',
					'product_sku' => 'product_retailer_part_no',
				),
				696 => array(
					'og_art_section' => 'article_section',
				),
				701 => array(
					'article_topic' => 'article_section',
				),
				725 => array(
					'product_volume_value' => 'product_fluid_volume_value',
				),
			),
		);

		private static $mod_defaults = array(

			/**
			 * Common elements.
			 */
			'id'          => 0,		// Post, term, or user ID.
			'name'        => false,		// Module name ('post', 'term', or 'user').
			'name_transl' => false,		// Module name translated.
			'obj'         => false,		// Module object.

			/**
			 * WpssoComment elements.
			 */
			'is_comment' => false,	// Is comment module.

			/**
			 * WpssoPage elements.
			 */
			'query_vars'  => array(),
			'is_404'      => false,
			'is_archive'  => false,
			'is_date'     => false,
			'is_day'      => false,
			'is_month'    => false,
			'is_public'   => true,
			'is_search'   => false,
			'is_year'     => false,

			/**
			 * WpssoPost elements.
			 */
			'use_post'             => false,
			'is_post'              => false,	// Is post module.
			'is_post_type_archive' => false,	// Post is an archive.
			'is_home'              => false,	// Home page (index or static)
			'is_home_page'         => false,	// Static front page.
			'is_home_posts'        => false,	// Static posts page or latest posts.
			'post_slug'            => false,	// Post name (aka slug).
			'post_type'            => false,	// Post type name.
			'post_type_label'      => false,	// Post type singular name.
			'post_mime'            => false,	// Post mime type (ie. image/jpg).
			'post_status'          => false,	// Post status name.
			'post_author'          => false,	// Post author id.
			'post_coauthors'       => array(),
			'post_time'            => false,	// Post published time (ISO 8601 date or false).
			'post_modified_time'   => false,	// Post modified time (ISO 8601 date or false).

			/**
			 * WpssoTerm elements.
			 */
			'is_term'   => false,	// Is term module.
			'tax_slug'  => '',	// Empty string by default.
			'tax_label' => false,	// Taxonomy singular name.

			/**
			 * WpssoUser elements.
			 */
			'is_user' => false,	// Is user module.
		);

		public function __construct( &$plugin ) {

			return self::must_be_extended();
		}

		/**
		 * Add WordPress action and filters hooks.
		 */
		public function add_wp_hooks() {

			return self::must_be_extended();
		}

		/**
		 * Get the $mod object for a post, term, or user ID.
		 */
		public function get_mod( $mod_id ) {

			return self::must_be_extended( self::$mod_defaults );
		}

		public static function get_mod_defaults() {

			return self::$mod_defaults;
		}

		/**
		 * Get the $mod object for the home page.
		 */
		public static function get_mod_home() {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			$mod = self::$mod_defaults;

			$post_id = 0;

			if ( 'page' === get_option( 'show_on_front' ) ) {

				if ( ! $post_id = (int) get_option( 'page_on_front', $default = 0 ) ) {

					$post_id = (int) get_option( 'page_for_posts', $default = 0 );
				}

				$mod = $wpsso->post->get_mod( $post_id );
			}

			/**
			 * Post elements.
			 */
			$mod[ 'is_home' ] = true;

			return $mod;
		}

		/**
		 * Option handling methods:
		 *
		 *	get_defaults()
		 *	get_options()
		 *	save_options()
		 *	delete_options()
		 */
		public function get_defaults( $mod_id, $md_key = false ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->log_args( array( 
					'mod_id' => $mod_id,
					'md_key' => $md_key,
				) );
			}

			static $local_cache = array();

			if ( ! isset( $local_cache[ $mod_id ] ) ) {

				$local_cache[ $mod_id ] = array();

			} elseif ( $this->md_cache_disabled ) {

				$local_cache[ $mod_id ] = array();
			}

			$md_defs =& $local_cache[ $mod_id ];	// Shortcut variable name.

			if ( ! WpssoOptions::can_cache() || empty( $md_defs[ 'options_filtered' ] ) ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'get_md_defaults filter allowed' );
				}

				$mod = $this->get_mod( $mod_id );

				$opts =& $this->p->options;		// Shortcut variable name.

				$def_og_type         = $this->p->og->get_mod_og_type( $mod, $get_ns = false, $use_mod_opts = false );
				$def_schema_type     = $this->p->schema->get_mod_schema_type( $mod, $get_id = true, $use_mod_opts = false );
				$def_primary_term_id = $this->p->post->get_default_term_id( $mod, $tax_slug = 'category' );	// Returns term ID or false.
				$def_reading_mins    = $this->p->page->get_reading_mins( $mod );

				$md_defs = array(
					'options_filtered'  => 0,
					'options_version'   => '',
					'attach_img_crop_x' => 'none',
					'attach_img_crop_y' => 'none',

					/**
					 * Customize tab.
					 */
					'schema_type'     => $def_schema_type,		// Schema Type.
					'og_type'         => $def_og_type,		// Open Graph Type.
					'primary_term_id' => $def_primary_term_id,	// Primary Category.
					'og_title'        => '',			// Default Title.
					'og_desc'         => '',			// Default Description.
					'p_img_desc'      => '',			// Pinterest Description.
					'tc_desc'         => '',			// Twitter Card Description.
					'seo_desc'        => '',			// Search Description.
					'sharing_url'     => '',			// Sharing URL.
					'canonical_url'   => '',			// Canonical URL.

					/**
					 * Open Graph article type.
					 */
					'article_section' => isset( $opts[ 'og_def_article_section' ] ) ? $opts[ 'og_def_article_section' ] : 'none',
					'reading_mins'    => $def_reading_mins,

					/**
					 * Open Graph book type.
					 */
					'book_isbn' => '',

					/**
					 * Open Graph product type.
					 */
					'product_category'         => isset( $opts[ 'og_def_product_category' ] ) ? $opts[ 'og_def_product_category' ] : 'none',
					'product_avail'            => 'none',
					'product_brand'            => '',
					'product_color'            => '',
					'product_condition'        => 'none',
					'product_currency'         => empty( $opts[ 'og_def_currency' ] ) ? 'USD' : $opts[ 'og_def_currency' ],
					'product_isbn'             => '',
					'product_material'         => '',
					'product_mfr_part_no'      => '',	// Product MPN.
					'product_price'            => '0.00',
					'product_retailer_part_no' => '',	// Product SKU.
					'product_size'             => '',
					'product_target_gender'    => 'none',

					/**
					 * Open Graph priority image.
					 */
					'og_img_max'    => isset( $opts[ 'og_img_max' ] ) ? (int) $opts[ 'og_img_max' ] : 1,	// 1 by default.
					'og_img_id'     => '',
					'og_img_id_pre' => empty( $opts[ 'og_def_img_id_pre' ] ) ? '' : $opts[ 'og_def_img_id_pre' ],	// Default library prefix.
					'og_img_url'    => '',

					/**
					 * Open Graph priority video.
					 */
					'og_vid_max'      => isset( $opts[ 'og_vid_max' ] ) ? (int) $opts[ 'og_vid_max' ] : 1,	// 1 by default.
					'og_vid_autoplay' => empty( $opts[ 'og_vid_autoplay' ] ) ? 0 : 1,	// Enabled by default.
					'og_vid_prev_img' => empty( $opts[ 'og_vid_prev_img' ] ) ? 0 : 1,	// Enabled by default.
					'og_vid_width'    => '',	// Custom value for first video.
					'og_vid_height'   => '',	// Custom value for first video.
					'og_vid_embed'    => '',
					'og_vid_url'      => '',
					'og_vid_title'    => '',	// Custom value for first video.
					'og_vid_desc'     => '',	// Custom value for first video.

					/**
					 * Pinterest.
					 */
					'p_img_id'     => '',
					'p_img_id_pre' => empty( $opts[ 'og_def_img_id_pre' ] ) ? '' : $opts[ 'og_def_img_id_pre' ],	// Default library prefix.
					'p_img_url'    => '',

					/**
					 * Twitter Card.
					 */
					'tc_lrg_img_id'     => '',
					'tc_lrg_img_id_pre' => empty( $opts[ 'og_def_img_id_pre' ] ) ? '' : $opts[ 'og_def_img_id_pre' ],	// Default library prefix.
					'tc_lrg_img_url'    => '',

					'tc_sum_img_id'     => '',
					'tc_sum_img_id_pre' => empty( $opts[ 'og_def_img_id_pre' ] ) ? '' : $opts[ 'og_def_img_id_pre' ],	// Default library prefix.
					'tc_sum_img_url'    => '',

					/**
					 * Schema JSON-LD Markup / Google Rich Results.
					 */
					'schema_img_max'    => isset( $opts[ 'schema_img_max' ] ) ? (int) $opts[ 'schema_img_max' ] : 1,	// 1 by default.
					'schema_img_id'     => '',
					'schema_img_id_pre' => empty( $opts[ 'og_def_img_id_pre' ] ) ? '' : $opts[ 'og_def_img_id_pre' ],	// Default library prefix.
					'schema_img_url'    => '',

					/**
					 * Gravity View (Side Metabox).
					 */
					'gv_id_title' => 0,	// Title Field ID
					'gv_id_desc'  => 0,	// Description Field ID
					'gv_id_img'   => 0,	// Post Image Field ID
				);

				/**
				 * See https://developers.google.com/search/reference/robots_meta_tag.
				 */
				$directives = SucomUtilRobots::get_default_directives();

				foreach ( $directives as $directive_key => $directive_value ) {

					$opt_key = str_replace( '-', '_', 'robots_' . $directive_key );	// Convert dashes to underscores.

					/**
					 * Use a default value from the plugin settings, if one exists.
					 */
					if ( isset( $this->p->options[ $opt_key ] ) ) {

						$md_defs[ $opt_key ] = $this->p->options[ $opt_key ];

					/**
					 * Save as a checkbox:
					 *
					 *	'robots_noarchive'
					 *	'robots_nofollow'
					 *	'robots_noimageindex'
					 *	'robots_noindex'
					 *	'robots_nosnippet'
					 *	'robots_notranslate'
					 */
					} elseif ( 0 === strpos( $directive_key, 'no' ) && is_bool( $directive_value ) ) {

						$md_defs[ $opt_key ] = $directive_value ? 1 : 0;

					/**
					 * Save the directive value:
					 *
					 *	'robots_max_snippet'
					 *	'robots_max_image_preview'
					 *	'robots_max_video_preview'
					 */
					} elseif ( 0 === strpos( $directive_key, 'max' ) ) {

						$md_defs[ $opt_key ] = $directive_value;
					}
				}

				if ( WpssoOptions::can_cache() ) {

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log( 'setting options_filtered to 1' );
					}

					$md_defs[ 'options_filtered' ] = 1;	// Set before calling filter to prevent recursion.

				} elseif ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'options_filtered value unchanged' );
				}

				/**
				 * Since WPSSO Core v3.28.0.
				 */
				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'applying get_md_defaults filters' );
				}

				$md_defs = apply_filters( 'wpsso_get_md_defaults', $md_defs, $mod );

				/**
				 * Since WPSSO Core v8.2.0.
				 */
				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'applying sanitize_md_defaults filters' );
				}

				$md_defs = apply_filters( 'wpsso_sanitize_md_defaults', $md_defs, $mod );

			} elseif ( $this->p->debug->enabled ) {

				$this->p->debug->log( 'get_md_defaults filter skipped' );
			}

			if ( false !== $md_key ) {

				if ( isset( $md_defs[ $md_key ] ) ) {

					return $md_defs[ $md_key ];
				}

				return null;
			}

			return $md_defs;
		}

		public function get_options( $mod_id, $md_key = false, $filter_opts = true, $pad_opts = false ) {

			$ret_val = false === $md_key ? array() : null;	// Allow for $md_key = 0.

			return self::must_be_extended( $ret_val );
		}

		/**
		 * Check if options need to be upgraded.
		 *
		 * Returns true or false.
		 */
		protected function is_upgrade_options_required( $md_opts ) {

			/**
			 * Example: 'options_version' = '-wpsso774std-wpssojson43std-wpssoum7std'.
			 */
			if ( isset( $md_opts[ 'options_version' ] ) && $md_opts[ 'options_version' ] !== $this->p->cf[ 'opt' ][ 'version' ] ) {

				return true;
			}

			return false;
		}

		protected function upgrade_options( $md_opts, $mod_id ) {

			/**
			 * Save / create the current options version number for version checks to follow.
			 */
			$prev_version = empty( $md_opts[ 'plugin_wpsso_opt_version' ] ) ? 0 : $md_opts[ 'plugin_wpsso_opt_version' ];

			$keys_by_ext = apply_filters( 'wpsso_rename_md_options_keys', self::$rename_keys_by_ext );

			$md_opts = $this->p->util->rename_options_by_ext( $md_opts, $keys_by_ext );

			/**
			 * Check for schema type IDs that need to be renamed.
			 */
			$schema_type_keys_preg = 'schema_type|plm_place_schema_type';

			foreach ( SucomUtil::preg_grep_keys( '/^(' . $schema_type_keys_preg . ')(_[0-9]+)?$/', $md_opts ) as $md_key => $md_val ) {

				if ( ! empty( $this->p->cf[ 'head' ][ 'schema_renamed' ][ $md_val ] ) ) {

					$md_opts[ $md_key ] = $this->p->cf[ 'head' ][ 'schema_renamed' ][ $md_val ];
				}
			}

			/**
			 * Import and delete deprecated robots post metadata.
			 */
			if ( $prev_version > 0 && $prev_version <= 759 ) {

				foreach ( array(
					'noarchive',
					'nofollow',
					'noimageindex',
					'noindex',
					'nosnippet',
					'notranslate',
				) as $directive_key ) {

					$opt_key = 'robots_' . $directive_key;

					$meta_key = '_wpsso_' . $directive_key;

					$directive_value = static::get_meta( $mod_id, $meta_key, $single = true );	// Use static method from child.

					if ( '' !== $directive_value ) {

						$md_opts[ $opt_key ] = (int) $directive_value;

						static::delete_meta( $mod_id, $meta_key );	// Use static method from child.
					}
				}
			}

			$md_opts = apply_filters( 'wpsso_upgraded_md_options', $md_opts );

			/**
			 * Mark options as current.
			 */
			$md_opts[ 'options_version' ] = $this->p->cf[ 'opt' ][ 'version' ];

			return $md_opts;
		}

		/**
		 * Do not pass $md_opts by reference as the options array may get padded with default values.
		 */
		protected function return_options( $mod_id, array $md_opts, $md_key = false, $pad_opts = false ) {

			if ( $pad_opts ) {

				if ( empty( $md_opts[ 'options_padded' ] ) ) {

					$md_defs = $this->get_defaults( $mod_id );

					if ( is_array( $md_defs ) ) {	// Just in case.

						foreach ( $md_defs as $md_defs_key => $md_defs_val ) {

							if ( ! isset( $md_opts[ $md_defs_key ] ) && $md_defs_val !== '' ) {

								$md_opts[ $md_defs_key ] = $md_defs[ $md_defs_key ];
							}
						}
					}

					$md_opts[ 'options_padded' ] = true;
				}
			}

			if ( false !== $md_key ) {

				if ( ! isset( $md_opts[ $md_key ] ) || $md_opts[ $md_key ] === '' ) {

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log( 'returning null value: ' . $md_key . ' not set or empty string' );
					}

					return null;
				}

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'returning meta value: ' . $md_key . ' = ' . $md_opts[ $md_key ] );
				}

				return $md_opts[ $md_key ];
			}

			return $md_opts;
		}

		public function save_options( $mod_id, $rel_id = false ) {

			return self::must_be_extended( $ret_val = false );
		}

		public function delete_options( $mod_id, $rel_id = false ) {

			return self::must_be_extended( $ret_val = false );
		}

		/**
		 * Get all publicly accessible post, term, or user IDs.
		 */
		public static function get_public_ids() {

			return self::must_be_extended( $ret_val = array() );
		}

		/**
		 * Return an array of post IDs for a given $mod object.
		 *
		 * Called by WpssoWpMeta->get_posts_mods().
		 */
		public function get_posts_ids( array $mod, array $extra_args = array() ) {

			return self::must_be_extended( $ret_val = array() );	// Return an empty array.
		}

		public function get_posts_mods( array $mod, array $extra_args = array() ) {

			$posts_mods = array();

			foreach ( $this->get_posts_ids( $mod, $extra_args ) as $post_id ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'getting mod for post object ID ' . $post_id );
				}

				$posts_mods[] = $this->p->post->get_mod( $post_id );
			}

			return $posts_mods;
		}

		public function add_meta_boxes() {

			return self::must_be_extended();
		}

		public function ajax_get_metabox_document_meta() {

			self::must_be_extended();

			die( -1 );	// Nothing to do.
		}

		public function show_metabox_document_meta( $obj ) {

			echo $this->get_metabox_document_meta( $obj );
		}

		public function get_metabox_document_meta( $obj ) {

			return self::must_be_extended( $ret_val = '' );	 // Empty html.
		}

		public function get_metabox_javascript( $container_id ) {

			$doing_ajax   = SucomUtilWP::doing_ajax();
			$container_id = empty( $container_id ) ? '' : '#' . $container_id;
			$metabox_html = '';

			if ( $doing_ajax ) {

				$metabox_html .= '<!-- metabox javascript for ajax call -->' . "\n";

				$metabox_html .= '<script type="text/javascript">sucomInitMetabox( \'' . $container_id . '\', true );</script>' . "\n";
			}

			return $metabox_html;
		}

		/**
		 * Does this page have a Document SSO metabox?
		 *
		 * If this is a post/term/user editing page, then the self::$head_tags variable will be an array.
		 */
		public static function is_meta_page() {

			if ( is_array( self::$head_tags ) ) {

				return true;
			}

			return false;
		}

		public static function get_head_tags() {

			return self::$head_tags;
		}

		protected function get_document_meta_tabs( $metabox_id, array $mod ) {

			$tabs = array();

			switch ( $metabox_id ) {

				case $this->p->cf[ 'meta' ][ 'id' ]:	// 'sso' metabox ID.

					$tabs[ 'edit' ]  = _x( 'Customize', 'metabox tab', 'wpsso' );
					$tabs[ 'media' ] = _x( 'Priority Media', 'metabox tab', 'wpsso' );

					if ( $mod[ 'is_public' ] ) {	// Since WPSSO Core v7.0.0.

						$tabs[ 'robots' ]   = _x( 'Robots Meta', 'metabox tab', 'wpsso' );	// Since WPSSO Core v8.4.0.
						$tabs[ 'preview' ]  = _x( 'Preview', 'metabox tab', 'wpsso' );
						$tabs[ 'oembed' ]   = _x( 'oEmbed', 'metabox tab', 'wpsso' );
						$tabs[ 'head' ]     = _x( 'Head Markup', 'metabox tab', 'wpsso' );
						$tabs[ 'validate' ] = _x( 'Validators', 'metabox tab', 'wpsso' );
					}

					break;
			}

			/**
			 * Exclude the 'Priority Media' tab from attachment editing pages.
			 */
			if ( $mod[ 'post_type' ] === 'attachment' ) {

				unset( $tabs[ 'media' ] );
			}

			/**
			 * Exclude the 'oEmbed' tab from non-post editing pages.
			 *
			 * get_oembed_response_data() is available since WP v4.4.
			 */
			if ( ! function_exists( 'get_oembed_response_data' ) ||	! $mod[ 'is_post' ] || ! $mod[ 'id' ] ) {

				unset( $tabs[ 'oembed' ] );
			}

			return apply_filters( 'wpsso_' . $mod[ 'name' ] . '_document_meta_tabs', $tabs, $mod, $metabox_id );
		}

		public function get_table_rows( $metabox_id, $tab_key, array $head_info, array $mod ) {

			$table_rows  = array();

			/**
			 * Call the following methods:
			 *
			 *	get_table_rows_sso_edit_tab()
			 *	get_table_rows_sso_media_tab()
			 *	get_table_rows_sso_preview_tab()
			 *	get_table_rows_sso_oembed_tab()
			 *	get_table_rows_sso_head_tab()
			 *	get_table_rows_sso_validate_tab()
			 */
			$method_name = 'get_table_rows_' . $metabox_id . '_' . $tab_key . '_tab';

			if ( method_exists( $this->edit, $method_name ) ) {

				$table_rows = call_user_func( array( $this->edit, $method_name ), $this->form, $head_info, $mod );
			}

			return $table_rows;
		}

		public function get_head_info( $mod_id, $read_cache = true ) {

			static $local_cache = array();

			if ( isset( $local_cache[ $mod_id ] ) ) {

				return $local_cache[ $mod_id ];
			}

			$mod = $this->get_mod( $mod_id );

			$head_tags = $this->p->head->get_head_array( $use_post = false, $mod, $read_cache );

			$head_info = $this->p->head->extract_head_info( $mod, $head_tags );

			return $local_cache[ $mod_id ] = $head_info;
		}

		/**
		 * Return a specific option from the custom social settings meta with fallback for multiple option keys. If $md_key
		 * is an array, then get the first non-empty option from the options array. This is an easy way to provide a
		 * fallback value for the first array key. Use 'none' as a key name to skip this fallback behavior.
		 *
		 * Example: get_options_multi( $mod_id, array( 'seo_desc', 'og_desc' ) );
		 */
		public function get_options_multi( $mod_id, $md_key = false, $filter_opts = true ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->log_args( array( 
					'mod_id'      => $mod_id,
					'md_key'      => $md_key,
					'filter_opts' => $filter_opts,
				) );
			}

			if ( empty( $mod_id ) ) {

				return null;
			}

			if ( false === $md_key ) {					// Return the whole options array.

				$md_val = $this->get_options( $mod_id, $md_key, $filter_opts );

			} elseif ( true === $md_key ) {					// True is not valid for a custom meta key.

				$md_val = null;

			} else {							// Return the first matching index value.

				if ( is_array( $md_key ) ) {

					$check_md_keys = array_unique( $md_key );	// Prevent duplicate key values.

				} else {

					$check_md_keys = array( $md_key );		// Convert a string to an array.
				}

				foreach ( $check_md_keys as $md_key ) {

					if ( 'none' === $md_key ) {			// Special index keyword - stop here.

						return null;

					} elseif ( empty( $md_key ) ) {			// Skip empty array keys.

						continue;

					} elseif ( is_array( $md_key ) ) {		// An array of arrays is not valid.

						continue;

					} else {

						if ( $this->p->debug->enabled ) {

							$this->p->debug->log( 'getting id ' . $mod_id . ' option ' . $md_key . ' value' );
						}

						if ( ( $md_val = $this->get_options( $mod_id, $md_key, $filter_opts ) ) !== null ) {

							if ( $this->p->debug->enabled ) {

								$this->p->debug->log( 'option ' . $md_key . ' value found (not null)' );
							}

							break;	// Stop after first match.
						}
					}
				}
			}

			if ( $md_val !== null ) {

				if ( $this->p->debug->enabled ) {

					$mod = $this->get_mod( $mod_id );

					$this->p->debug->log( 'custom ' . $mod[ 'name' ] . ' ' . ( false === $md_key ? 'options' : 
						( is_array( $md_key ) ? implode( $glue = ', ', $md_key ) : $md_key ) ) . ' = ' . 
						( is_array( $md_val ) ? print_r( $md_val, true ) : '"' . $md_val . '"' ) );
				}
			}

			return $md_val;
		}

		public function user_can_edit( $mod_id, $rel_id = false ) {

			return self::must_be_extended( $ret_val = false );	// Return false by default.
		}

		public function user_can_save( $mod_id, $rel_id = false ) {

			return self::must_be_extended( $ret_val = false );	// Return false by default.
		}

		public function clear_cache( $mod_id, $rel_id = false ) {

			return self::must_be_extended();
		}

		protected function clear_mod_cache( array $mod, array $cache_types = array(), $sharing_url = false ) {

			if ( false === $sharing_url ) {

				$sharing_url = $this->p->util->get_sharing_url( $mod );
			}

			$mod_salt = SucomUtil::get_mod_salt( $mod, $sharing_url );

			$cache_types[ 'transient' ][] = array(
				'id'   => 'wpsso_h_' . md5( 'WpssoHead::get_head_array(' . $mod_salt . ')' ),
				'pre'  => 'wpsso_h_',
				'salt' => 'WpssoHead::get_head_array(' . $mod_salt . ')',
			);

			$cache_types[ 'wp_cache' ][] = array(
				'id'   => 'wpsso_c_' . md5( 'WpssoPage::get_the_content(' . $mod_salt . ')' ),
				'pre'  => 'wpsso_c_',
				'salt' => 'WpssoPage::get_the_content(' . $mod_salt . ')',
			);

			/**
			 * WordPress stores data using a post, term, or user ID, along with a group string.
			 *
			 * Example: wp_cache_get( 1, 'user_meta' );
			 */
			$cleared_count = wp_cache_delete( $mod[ 'id' ], $mod[ 'name' ] . '_meta' );

			$cleared_ids = array();

			foreach ( $cache_types as $type_name => $type_keys ) {

				/**
				 * $filter_name example: 'wpsso_post_cache_transient_keys'.
				 */
				$filter_name = 'wpsso_' . $mod[ 'name' ] . '_cache_' . $type_name . '_keys';

				$type_keys = (array) apply_filters( $filter_name, $type_keys, $mod, $sharing_url, $mod_salt );

				foreach ( $type_keys as $mixed ) {

					if ( is_array( $mixed ) && isset( $mixed[ 'id' ] ) ) {

						$cache_id = $mixed[ 'id' ];

						$cache_key = '';
						$cache_key .= isset( $mixed[ 'pre' ] ) ? $mixed[ 'pre' ] : '';
						$cache_key .= isset( $mixed[ 'salt' ] ) ? $mixed[ 'salt' ] : '';
						$cache_key = trim( $cache_key );

						if ( empty( $cache_key ) ) {

							$cache_key = $cache_id;
						}

					} else {

						$cache_id = $cache_key = $mixed;
					}

					if ( isset( $cleared_ids[ $type_name ][ $cache_id ] ) ) {	// skip duplicate cache ids

						continue;
					}

					switch ( $type_name ) {

						case 'transient':

							$ret = delete_transient( $cache_id );

							break;

						case 'wp_cache':

							$ret = wp_cache_delete( $cache_id );

							break;

						default:

							$ret = false;

							break;
					}

					if ( $ret ) {

						$cleared_count++;
					}

					$cleared_ids[ $type_name ][ $cache_key ] = $ret;
				}
			}

			return $cleared_count;
		}

		protected function verify_submit_nonce() {

			if ( empty( $_POST ) ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'empty POST for submit' );
				}

				return false;

			}

			if ( empty( $_POST[ WPSSO_NONCE_NAME ] ) ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'submit POST missing nonce token' );
				}

				return false;

			}

			if ( ! wp_verify_nonce( $_POST[ WPSSO_NONCE_NAME ], WpssoAdmin::get_nonce_action() ) ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'submit nonce token validation failed' );
				}

				if ( is_admin() ) {

					$this->p->notice->err( __( 'Nonce token validation failed for the submitted form (update ignored).',
						'wpsso' ) );
				}

				return false;

			}

			return true;
		}

		protected function get_submit_opts( $mod_id ) {

			$mod     = $this->get_mod( $mod_id );
			$md_defs = $this->get_defaults( $mod[ 'id' ] );
			$md_prev = $this->get_options( $mod[ 'id' ] );

			/**
			 * Remove plugin version strings.
			 */
			$md_unset_keys = array( 'options_filtered', 'options_version' );

			foreach ( $this->p->cf[ 'plugin' ] as $ext => $info ) {

				if ( isset( $info[ 'opt_version' ] ) ) {

					$md_unset_keys[] = 'plugin_' . $ext . '_opt_version';
				}
			}

			foreach ( $md_unset_keys as $md_key ) {

				unset( $md_defs[ $md_key ], $md_prev[ $md_key ] );
			}

			/**
			 * Merge and sanitize the new options.
			 */
			$md_opts = empty( $_POST[ WPSSO_META_NAME ] ) ? array() : $_POST[ WPSSO_META_NAME ];
			$md_opts = SucomUtil::restore_checkboxes( $md_opts );
			$md_opts = array_merge( $md_prev, $md_opts );	// Update the previous options array.
			$md_opts = $this->p->opt->sanitize( $md_opts, $md_defs, $network = false, $mod );

			/**
			 * Check image size options (id, prefix, width, height, crop, etc.).
			 */
			foreach ( array( 'og', 'p', 'schema', 'tc_lrg', 'tc_sum' ) as $md_pre ) {

				/**
				 * If there's no image ID, then remove the image ID library prefix.
				 *
				 * If an image ID is being used, then remove the image URL (only one can be defined).
				 */
				if ( empty( $md_opts[ $md_pre . '_img_id' ] ) ) {

					unset( $md_opts[ $md_pre . '_img_id_pre' ] );

				} else {

					unset( $md_opts[ $md_pre . '_img_url' ] );
				}
			}

			/**
			 * Remove empty strings, "use plugin settings", or "same as default" option values.
			 *
			 * Use strict comparison to manage conversion (don't allow string to integer conversion, for example).
			 */
			foreach ( $md_opts as $md_key => $md_val ) {

				if ( '' === $md_val ) {

					unset( $md_opts[ $md_key ] );

				} elseif ( isset( $md_defs[ $md_key ] ) && ( $md_val === $md_defs[ $md_key ] || $md_val === (string) $md_defs[ $md_key ] ) ) {

					unset( $md_opts[ $md_key ] );

				} elseif ( $md_val === WPSSO_UNDEF || $md_val === (string) WPSSO_UNDEF ) {

					switch ( $md_key ) {

						case 'robots_max_snippet':
						case 'robots_max_video_preview':

							break;

						default:

							unset( $md_opts[ $md_key ] );
					}
				}
			}

			/**
			 * Re-number multi options (example: schema type url, recipe ingredient, recipe instruction, etc.).
			 */
			foreach ( $this->p->cf[ 'opt' ][ 'cf_md_multi' ] as $md_multi => $is_multi ) {

				if ( empty( $is_multi ) ) {	// True, false, or array.

					continue;
				}

				/**
				 * Get multi option values indexed only by their number.
				 */
				$md_multi_opts = SucomUtil::preg_grep_keys( '/^' . $md_multi . '_([0-9]+)$/', $md_opts, $invert = false, $replace = '$1' );

				$md_renum_opts = array();	// Start with a fresh array.

				$renum = 0;	// Start a new index at 0.

				foreach ( $md_multi_opts as $md_num => $md_val ) {

					if ( $md_val !== '' ) {	// Only save non-empty values.

						$md_renum_opts[ $md_multi . '_' . $renum ] = $md_val;
					}

					/**
					 * Check if there are linked options, and if so, re-number those options as well.
					 */
					if ( is_array( $is_multi ) ) {

						foreach ( $is_multi as $md_multi_linked ) {

							if ( isset( $md_opts[ $md_multi_linked . '_' . $md_num ] ) ) {	// Just in case.

								$md_renum_opts[ $md_multi_linked . '_' . $renum ] = $md_opts[ $md_multi_linked . '_' . $md_num ];
							}
						}
					}

					$renum++;	// Increment the new index number.
				}

				/**
				 * Remove any existing multi options, including any linked options.
				 */
				$md_opts = SucomUtil::preg_grep_keys( '/^' . $md_multi . '_([0-9]+)$/', $md_opts, $invert = true );

				if ( is_array( $is_multi ) ) {

					foreach ( $is_multi as $md_multi_linked ) {

						$md_opts = SucomUtil::preg_grep_keys( '/^' . $md_multi_linked . '_([0-9]+)$/', $md_opts, $invert = true );
					}
				}

				/**
				 * Save the re-numbered options.
				 */
				foreach ( $md_renum_opts as $md_key => $md_val ) {

					$md_opts[ $md_key ] = $md_val;
				}

				unset( $md_renum_opts );
			}

			/**
			 * Mark the new options as current.
			 */
			$md_opts[ 'options_version' ] = $this->p->cf[ 'opt' ][ 'version' ];

			foreach ( $this->p->cf[ 'plugin' ] as $ext => $info ) {

				if ( isset( $info[ 'opt_version' ] ) ) {

					$md_opts[ 'plugin_' . $ext . '_opt_version' ] = $info[ 'opt_version' ];
				}
			}

			return $md_opts;
		}

		/**
		 * Return translated column headers.
		 *
		 * Called by add_column_headings(), WpssoStdAdminAdvanced->filter_edit_table_columns_rows(), and WpssoProAdminAdvanced->filter_edit_table_columns_rows().
		 */
		public static function get_column_headers() {

			$headers = array();

			$sortable_cols = self::get_sortable_columns();

			foreach ( $sortable_cols as $col_key => $col_info ) {

				if ( ! empty( $col_info[ 'header' ] ) ) {

					$headers[ $col_key ] = _x( $col_info[ 'header' ], 'column header', 'wpsso' );
				}
			}

			return $headers;
		}

		/**
		 * Return sortable column keys and their query sort info.
		 */
		public static function get_sortable_columns( $col_key = false ) {

			static $local_cache = null;

			if ( null === $local_cache ) {

				$wpsso =& Wpsso::get_instance();

				$local_cache = (array) apply_filters( 'wpsso_get_sortable_columns', $wpsso->cf[ 'edit' ][ 'columns' ] );

				if ( $wpsso->debug->enabled ) {

					$wpsso->debug->log_arr( 'sortable columns', $local_cache );
				}
			}

			if ( false !== $col_key ) {

				if ( isset( $local_cache[ $col_key ] ) ) {

					return $local_cache[ $col_key ];
				}

				return null;

			}

			return $local_cache;
		}

		public static function get_column_meta_keys() {

			$meta_keys = array();

			$sortable_cols = self::get_sortable_columns();

			foreach ( $sortable_cols as $col_key => $col_info ) {

				if ( ! empty( $col_info[ 'meta_key' ] ) ) {

					$meta_keys[ $col_key ] = $col_info[ 'meta_key' ];
				}
			}

			return $meta_keys;
		}

		public function get_column_wp_cache( array $mod, $column_name ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$column_val = '';

			if ( ! empty( $mod[ 'id' ] ) && strpos( $column_name, 'wpsso_' ) === 0 ) {	// Just in case.

				$col_key = str_replace( 'wpsso_', '', $column_name );

				if ( ( $col_info = self::get_sortable_columns( $col_key ) ) !== null ) {

					if ( isset( $col_info[ 'meta_key' ] ) ) {	// Just in case.

						if ( $this->p->debug->enabled ) {

							$this->p->debug->log( 'getting meta cache for ' . $mod[ 'name' ] . ' id ' . $mod[ 'id' ] );
						}

						/**
						 * WordPress stores data using a post, term, or user ID, along with a group string.
						 *
						 * Example: wp_cache_get( 1, 'user_meta' );
						 *
						 * Returns (bool|mixed) false on failure to retrieve contents or the cache contents on success.
						 *
						 * $found (bool) (Optional) whether the key was found in the cache (passed by reference). Disambiguates a
						 * return of false, a storable value. Default null.
						 */
						$meta_cache = wp_cache_get( $mod[ 'id' ], $mod[ 'name' ] . '_meta', $force = false, $found );

						if ( ! $found ) {

							if ( $this->p->debug->enabled ) {

								$this->p->debug->log( 'updating meta cache for ' . $mod[ 'name' ] . ' id ' . $mod[ 'id' ] );
							}

							$meta_cache = update_meta_cache( $mod[ 'name' ], array( $mod[ 'id' ] ) );

							$meta_cache = $meta_cache[ $mod[ 'id' ] ];
						}

						if ( isset( $meta_cache[ $col_info[ 'meta_key' ] ] ) ) {

							$column_val = (string) maybe_unserialize( $meta_cache[ $col_info[ 'meta_key' ] ][ 0 ] );
						}
					}
				}
			}

			return $column_val;
		}

		public function get_column_content( $value, $column_name, $mod_id ) {

			if ( ! empty( $mod_id ) && 0 === strpos( $column_name, 'wpsso_' ) ) {	// Just in case.

				$col_key = str_replace( 'wpsso_', '', $column_name );

				if ( ( $col_info = self::get_sortable_columns( $col_key ) ) !== null ) {

					if ( isset( $col_info[ 'meta_key' ] ) ) {	// just in case

						$value = $this->get_meta_cache_value( $mod_id, $col_info[ 'meta_key' ] );
					}
				}
			}

			return $value;
		}

		public function get_meta_cache_value( $mod_id, $meta_key, $none = '' ) {

			return self::must_be_extended( $none );
		}

		/**
		 * Called by WpssoPost->check_sortable_post_metadata(), WpssoTerm->check_sortable_term_metadata(), and
		 * WpssoUser->check_sortable_user_metadata().
		 */
		protected function check_sortable_metadata( $value, $mod_id, $meta_key, $single ) {

			/**
			 * Example $meta_key value:
			 *
			 *	'_wpsso_head_info_og_img_thumb'.
			 */
			if ( 0 !== strpos( $meta_key, '_wpsso_head_info_' ) ) {

				return $value;	// Return null.
			}

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			static $local_no_recurs = array();

			if ( isset( $local_no_recurs[ $mod_id ][ $meta_key ] ) ) {

				return $value;	// Return null.
			}

			$local_no_recurs[ $mod_id ][ $meta_key ] = true;	// Prevent recursion.

			$meta_value = static::get_meta( $mod_id, $meta_key, $force_single = true );	// Use static method from child.

			if ( '' === $meta_value ) {

				$this->get_head_info( $mod_id, $read_cache = true );
			}

			unset( $local_no_recurs[ $mod_id ][ $meta_key ] );

			return $value;	// Return null.
		}

		public function update_sortable_meta( $mod_id, $col_key, $content ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( ! empty( $mod_id ) ) {	// Just in case.

				if ( ( $sortable_cols = self::get_sortable_columns( $col_key ) ) !== null ) {

					if ( isset( $sortable_cols[ 'meta_key' ] ) ) {	// Just in case.

						static::update_meta( $mod_id, $sortable_cols[ 'meta_key' ], $content );	// Use static method from child.
					}
				}
			}
		}

		public function add_sortable_columns( $columns ) {

			foreach ( self::get_sortable_columns() as $col_key => $col_info ) {

				if ( ! empty( $col_info[ 'orderby' ] ) ) {

					$columns[ 'wpsso_' . $col_key ] = 'wpsso_' . $col_key;
				}
			}

			return $columns;
		}

		public function set_column_orderby( $query ) {

			$col_name = $query->get( 'orderby' );

			if ( is_string( $col_name ) && strpos( $col_name, 'wpsso_' ) === 0 ) {

				$col_key = str_replace( 'wpsso_', '', $col_name );

				if ( ( $col_info = self::get_sortable_columns( $col_key ) ) !== null ) {

					foreach ( array( 'meta_key', 'orderby' ) as $set_name ) {

						if ( ! empty( $col_info[ $set_name ] ) ) {

							$query->set( $set_name, $col_info[ $set_name ] );
						}
					}
				}
			}
		}

		/**
		 * Called by WpssoPost->add_post_column_headings(), WpssoPost->add_media_column_headings(),
		 * WpssoTerm->add_term_column_headings(), and WpssoUser->add_user_column_headings().
		 */
		protected function add_column_headings( $columns, $list_type = '' ) {

			if ( ! empty( $list_type ) ) {

				foreach ( self::get_column_headers() as $col_key => $col_header ) {

					/**
					 * Check if the column is enabled globally for the post, media, term, or user edit list.
					 */
					if ( ! empty( $this->p->options[ 'plugin_' . $col_key . '_col_' . $list_type ] ) ) {

						if ( $this->p->debug->enabled ) {

							$this->p->debug->log( 'adding wpsso_' . $col_key . ' column' );
						}

						$columns[ 'wpsso_' . $col_key ] = $col_header;
					}
				}
			}

			return $columns;
		}

		/**
		 * Returns an array of single image associative arrays.
		 *
		 * $size_names can be a keyword (ie. 'opengraph' or 'schema'), a registered size name, or an array of size names.
		 *
		 * $md_pre can be a text string or array of prefixes.
		 */
		public function get_md_images( $num = 0, $size_names, array $mod, $check_dupes = true, $md_pre = 'og', $mt_pre = 'og' ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->log_args( array( 
					'num'         => $num,
					'size_names'  => $size_names,
					'mod'         => $mod,
					'check_dupes' => $check_dupes,
					'md_pre'      => $md_pre,
					'mt_pre'      => $mt_pre,
				), get_class( $this ) );
			}

			if ( empty( $mod[ 'id' ] ) ) {	// Just in case.

				return array();

			} elseif ( $num < 1 ) {	// Just in case.

				return array();
			}

			$size_names = $this->p->util->get_image_size_names( $size_names );	// Always returns an array.

			$md_pre = is_array( $md_pre ) ? array_merge( $md_pre, array( 'og' ) ) : array( $md_pre, 'og' );

			$mt_images  = array();

			foreach( array_unique( $md_pre ) as $opt_pre ) {

				if ( $opt_pre === 'none' ) {		// Special index keyword.

					break;

				} elseif ( empty( $opt_pre ) ) {	// Skip empty md_pre values.

					continue;
				}

				$pid = $this->get_options( $mod[ 'id' ], $opt_pre . '_img_id' );
				$pre = $this->get_options( $mod[ 'id' ], $opt_pre . '_img_id_pre' );
				$url = $this->get_options( $mod[ 'id' ], $opt_pre . '_img_url' );

				if ( $pid > 0 ) {

					$pid = $pre === 'ngg' ? 'ngg-' . $pid : $pid;

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log( 'using custom ' . $opt_pre . ' image ID = "' . $pid . '"', get_class( $this ) );
					}

					$mt_images = $this->p->media->get_mt_pid_images( $pid, $size_names, $check_dupes, $mt_pre );

				}

				if ( empty( $mt_images ) && $url ) {

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log( 'using custom ' . $opt_pre . ' image URL = "' . $url . '"', get_class( $this ) );
					}

					$img_width  = $this->get_options( $mod[ 'id' ], $opt_pre . '_img_url:width' );
					$img_height = $this->get_options( $mod[ 'id' ], $opt_pre . '_img_url:height' );

					$mt_single_image = SucomUtil::get_mt_image_seed( $mt_pre );

					$mt_single_image[ $mt_pre . ':image:url' ]    = $url;
					$mt_single_image[ $mt_pre . ':image:width' ]  = $img_width > 0 ? $img_width : WPSSO_UNDEF;
					$mt_single_image[ $mt_pre . ':image:height' ] = $img_height > 0 ? $img_height : WPSSO_UNDEF;

					if ( $this->p->util->push_max( $mt_images, $mt_single_image, $num ) ) {

						return $mt_images;
					}
				}

				if ( $pid || $url ) {	// Stop after first $md_pre image found.

					break;
				}
			}

			if ( $this->p->util->is_maxed( $mt_images, $num ) ) {

				return $mt_images;
			}

			$filter_name = 'wpsso_' . $mod[ 'name' ] . '_image_ids';

			$image_ids = apply_filters( $filter_name, array(), $size_names, $mod[ 'id' ], $mod );

			foreach ( $image_ids as $pid ) {

				if ( $pid > 0 ) {	// Quick sanity check.

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log( 'adding image pid: ' . $pid );
					}

					$mt_pid_images = $this->p->media->get_mt_pid_images( $pid, $size_names, $check_dupes, $mt_pre );

					if ( $this->p->util->merge_max( $mt_images, $mt_pid_images, $num ) ) {

						return $mt_images;
					}
				}
			}

			$filter_name = 'wpsso_' . $mod[ 'name' ] . '_image_urls';

			$image_urls = apply_filters( $filter_name, array(), $size_names, $mod[ 'id' ], $mod );

			foreach ( $image_urls as $url ) {

				if ( false !== strpos( $url, '://' ) ) {	// Quick sanity check.

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log( 'adding image URL: ' . $url );
					}

					$mt_single_image = SucomUtil::get_mt_image_seed( $mt_pre );

					$mt_single_image[ $mt_pre . ':image:url' ] = $url;

					$this->p->util->add_image_url_size( $mt_single_image, $mt_pre . ':image' );

					if ( $this->p->util->push_max( $mt_images, $mt_single_image, $num ) ) {

						return $mt_images;
					}
				}
			}

			return $mt_images;
		}

		/**
		 * Extended by the WpssoUser class to support non-WordPress user images.
		 *
		 * Returns an array of single image associative arrays.
		 *
		 * $md_pre can be a text string or array of prefixes.
		 */
		public function get_og_images( $num = 0, $size_names, $mod_id, $check_dupes = true, $md_pre = 'og', $mt_pre = 'og' ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$mod = $this->get_mod( $mod_id );

			return $this->get_md_images( $num, $size_names, $mod, $check_dupes, $md_pre, $mt_pre );
		}

		/**
		 * Returns an array of single video associative arrays.
		 *
		 * $md_pre can be a text string or array of prefixes.
		 */
		public function get_og_videos( $num = 0, $mod_id, $check_dupes = false, $md_pre = 'og', $mt_pre = 'og' ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->log_args( array( 
					'num'         => $num,
					'mod_id'      => $mod_id,
					'check_dupes' => $check_dupes,
					'md_pre'      => $md_pre,
					'mt_pre'      => $mt_pre,
				), get_class( $this ) );
			}

			if ( empty( $mod_id ) ) {	// Just in case.

				return array();

			} elseif ( $num < 1 ) {	// Just in case.

				return array();
			}

			$mod = $this->get_mod( $mod_id );	// Required for get_content_videos().

			$md_pre = is_array( $md_pre ) ? array_merge( $md_pre, array( 'og' ) ) : array( $md_pre, 'og' );

			$mt_videos = array();

			foreach( array_unique( $md_pre ) as $opt_pre ) {

				if ( $opt_pre === 'none' ) {		// Special index keyword.

					break;

				} elseif ( empty( $opt_pre ) ) {	// Skip empty md_pre values.

					continue;
				}

				$html = $this->get_options( $mod_id, $opt_pre . '_vid_embed' );
				$url  = $this->get_options( $mod_id, $opt_pre . '_vid_url' );

				if ( $html ) {

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log( 'fetching video(s) from custom ' . $opt_pre . ' embed code', get_class( $this ) );
					}

					/**
					 * Returns an array of single video associative arrays.
					 */
					$mt_videos = $this->p->media->get_content_videos( $num, $mod, $check_dupes, $html );
				}

				if ( empty( $mt_videos ) && $url && ( ! $check_dupes || $this->p->util->is_uniq_url( $url ) ) ) {

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log( 'fetching video from custom ' . $opt_pre . ' url ' . $url, get_class( $this ) );
					}

					$args = array(
						'url'      => $url,
						'width'    => WPSSO_UNDEF,
						'height'   => WPSSO_UNDEF,
						'type'     => '',
						'prev_url' => '',
						'post_id'  => null,
						'api'      => '',
					);

					/**
					 * Returns a single video associative array.
					 */
					$mt_single_video = $this->p->media->get_video_details( $args, $check_dupes, true );

					if ( ! empty( $mt_single_video ) ) {

						if ( $this->p->util->push_max( $mt_videos, $mt_single_video, $num ) ) {

							if ( $this->p->debug->enabled ) {

								$this->p->debug->log( 'returning ' . count( $mt_videos ) . ' videos' );
							}

							return $mt_videos;
						}
					}
				}

				if ( $html || $url ) {	// Stop after first $md_pre video found.

					break;
				}
			}

			return $mt_videos;
		}

		public function get_head_info_thumb_bg_img( $head_info, $mod, $md_pre = 'og', $mt_pre = 'og' ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark( 'getting thumbnail image' );	// Begin timer.
			}

			$media_html = '';

			if ( empty( $head_info[ $mt_pre . ':image:id' ] ) ) {

				$media_html .= '<!-- checking head info array for image URL -->';

			} else {

				$pid = $head_info[ $mt_pre . ':image:id' ];

				$size_name = 'thumbnail';

				/**
				 * get_mt_single_image_src() returns an og:image:url value, not an og:image:secure_url.
				 *
				 * Example:
				 *
				 * 	array(
				 *		$mt_pre . ':image:url'        => '',
				 *		$mt_pre . ':image:width'      => '',
				 *		$mt_pre . ':image:height'     => '',
				 *		$mt_pre . ':image:cropped'    => '',	// Non-standard / internal meta tag.
				 *		$mt_pre . ':image:id'         => '',	// Non-standard / internal meta tag.
				 *		$mt_pre . ':image:alt'        => '',
				 *		$mt_pre . ':image:size_name'  => '',	// Non-standard / internal meta tag.
				 * 	);
				 */
				$mt_single_image = $this->p->media->get_mt_single_image_src( $pid, $size_name, $check_dupes = false, $mt_pre );

				$media_html .= '<!-- getting ' . $size_name . ' size for image ID ' . $pid . ' = ';

				if ( empty( $mt_single_image[ $mt_pre . ':image:url' ] ) ) {	// Just in case.

					$media_html .= 'failed';

				} else {

					$media_html .= 'success';

					$head_info =& $mt_single_image;	// Use the updated image information.
				}

				$media_html .= ' -->';
			}

			$image_url = SucomUtil::get_first_mt_media_url( $head_info );

			if ( empty( $image_url ) ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'no thumbnail image URL' );
				}

			} else {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'thumbnail image URL = ' . $image_url );
				}

				$media_html .= '<div class="wp-thumb-bg-img" style="background-image:url(' . $image_url . ');"></div><!-- .wp-thumb-bg-img -->';
			}

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark( 'getting thumbnail image' );	// End timer.
			}

			return $media_html;
		}

		/**
		 * Deprecated on 2021/01/19.
		 */
		public function get_og_type_reviews( $mod_id, $mt_pre = 'product', $rating_meta = 'rating', $worst_rating = 1, $best_rating = 5 ) {

			return $this->get_mt_reviews( $mod_id, $mt_pre, $rating_meta, $worst_rating, $best_rating );
		}

		/**
		 * Methods that return an associative array of Open Graph meta tags.
		 */
		public function get_mt_reviews( $mod_id, $mt_pre = 'product', $rating_meta = 'rating', $worst_rating = 1, $best_rating = 5 ) {

			return self::must_be_extended( $ret_val = array() );	// Return an empty array.
		}

		public function get_mt_comment_review( $comment_obj, $mt_pre = 'product', $rating_meta = 'rating', $worst_rating = 1, $best_rating = 5 ) {

			$mt_ret = array();

			if ( empty( $comment_obj->comment_ID ) ) {	// Just in case.

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'exiting early: comment object ID is empty' );
				}

				return $mt_ret;
			}

			$mod          = $this->p->comment->get_mod( $comment_obj->comment_ID );
			$sharing_url  = $this->p->util->get_sharing_url( $mod );
			$img_ids_meta = apply_filters( 'wpsso_mt_comment_review_image_ids_meta_name', 'reviews-images' );

			$mt_ret[ $mt_pre . ':review:id' ]           = $mod[ 'id' ];
			$mt_ret[ $mt_pre . ':review:url' ]          = $sharing_url;
			$mt_ret[ $mt_pre . ':review:title' ]        = '';
			$mt_ret[ $mt_pre . ':review:content' ]      = get_comment_excerpt( $mod[ 'id' ] );
			$mt_ret[ $mt_pre . ':review:created_time' ] = mysql2date( 'c', $comment_obj->comment_date_gmt );

			/**
			 * Review author.
			 */
			$mt_ret[ $mt_pre . ':review:author:id' ]    = $comment_obj->user_id;		// Author ID if registered (0 otherwise).
			$mt_ret[ $mt_pre . ':review:author:name' ]  = $comment_obj->comment_author;	// Author display name.

			/**
			 * Review rating.
			 *
			 * Rating values must be larger than 0 to include rating info.
			 */
			$rating_value = (float) get_comment_meta( $mod[ 'id' ], $rating_meta, $single = true );

			if ( $rating_value > 0 ) {

				$mt_ret[ $mt_pre . ':review:rating:value' ] = $rating_value;
				$mt_ret[ $mt_pre . ':review:rating:worst' ] = $worst_rating;
				$mt_ret[ $mt_pre . ':review:rating:best' ]  = $best_rating;
			}

			/**
			 * Add comment / review images.
			 */
			$mt_ret[ $mt_pre . ':review:image' ] = array();

			$image_ids = get_comment_meta( $mod[ 'id' ], $img_ids_meta, $single = true );

			if ( is_array( $image_ids ) ) {

				/**
				 * Set the reference values for admin notices.
				 */
				if ( is_admin() ) {

					$this->p->util->maybe_set_ref( $sharing_url, $mod, __( 'adding schema images', 'wpsso' ) );
				}

				$size_names = $this->p->util->get_image_size_names( 'schema' );	// Always returns an array.

				$this->p->util->clear_uniq_urls( $size_names );	// Reset previously seen image URLs.

				foreach ( $image_ids as $pid ) {

					if ( $pid > 0 ) {	// Quick sanity check.

						if ( $this->p->debug->enabled ) {

							$this->p->debug->log( 'adding image pid: ' . $pid );
						}

						/**
						 * $size_names can be a keyword (ie. 'opengraph' or 'schema'), a registered size name, or an array of size names.
						 */
						$mt_pid_images = $this->p->media->get_mt_pid_images( $pid, $size_names, $check_dupes = true, $mt_pre . ':review' );

						if ( ! empty( $mt_pid_images ) ) {

							$this->p->util->merge_max( $mt_ret[ $mt_pre . ':review:image' ], $mt_pid_images );
						}
					}
				}

				if ( ! empty( $mt_ret[ $mt_pre . ':review:image' ] ) ) {	// No need to clear of no images were found.

					$this->p->util->clear_uniq_urls( $size_names );
				}

				/**
				 * Restore previous reference values for admin notices.
				 */
				if ( is_admin() ) {

					$this->p->util->maybe_unset_ref( $sharing_url );
				}
			}

			return $mt_ret;
		}

		/**
		 * WpssoPost class specific methods.
		 */
		public function get_sharing_shortlink( $shortlink = false, $mod_id = 0, $context = 'post', $allow_slugs = true ) {

			return self::must_be_extended( $ret_val = '' );
		}

		public function maybe_restore_shortlink( $shortlink = false, $mod_id = 0, $context = 'post', $allow_slugs = true ) {

			return self::must_be_extended( $ret_val = '' );
		}

		/**
		 * WpssoUser class specific methods.
		 *
		 * Called by WpssoOpenGraph->get_array() for a single post author and (possibly) several coauthors.
		 */
		public function get_authors_websites( $user_ids, $field_id = 'url' ) {

			return self::must_be_extended( $ret_val = array() );
		}

		public function get_author_website( $user_id, $field_id = 'url' ) {

			return self::must_be_extended( $ret_val = '' );
		}

		/**
		 * Since WPSSO Core v7.6.0.
		 */
		public static function get_attached( $mod_id, $attach_type ) {

			$opts = static::get_meta( $mod_id, WPSSO_META_ATTACHED_NAME, $single = true );		// Use static method from child.

			if ( isset( $opts[ $attach_type ] ) ) {

				if ( is_array( $opts[ $attach_type ] ) ) {	// Just in case.

					return $opts[ $attach_type ];
				}
			}

			return array();	// No values.
		}

		/**
		 * Since WPSSO Core v7.6.0.
		 *
		 * Used by WpssoFaqShortcodeQuestion->do_shortcode().
		 */
		public static function add_attached( $mod_id, $attach_type, $attachment_id ) {

			$opts = static::get_meta( $mod_id, WPSSO_META_ATTACHED_NAME, $single = true );		// Use static method from child.

			if ( ! isset( $opts[ $attach_type ][ $attachment_id ] ) ) {

				if ( ! is_array( $opts ) ) {

					$opts = array();
				}

				$opts[ $attach_type ][ $attachment_id ] = true;

				return static::update_meta( $mod_id, WPSSO_META_ATTACHED_NAME, $opts );		// Use static method from child.
			}

			return false;	// No addition.
		}

		/**
		 * Since WPSSO Core v7.6.0.
		 */
		public static function delete_attached( $mod_id, $attach_type, $attachment_id ) {

			$opts = static::get_meta( $mod_id, WPSSO_META_ATTACHED_NAME, $single = true );		// Use static method from child.

			if ( isset( $opts[ $attach_type ][ $attachment_id ] ) ) {

				unset( $opts[ $attach_type ][ $attachment_id ] );

				if ( empty( $opts ) ) {	// Cleanup.

					return static::delete_meta( $mod_id, WPSSO_META_ATTACHED_NAME );	// Use static method from child.
				}

				return static::update_meta( $mod_id, WPSSO_META_ATTACHED_NAME, $opts );		// Use static method from child.
			}

			return false;	// No delete.
		}

		/**
		 * Since WPSSO Core v8.15.0.
		 *
		 * Returns a custom or default term ID, or false if a term for the $tax_slug is not found.
		 */
		public function get_primary_term_id( array $mod, $tax_slug = 'category' ) {

			return self::must_be_extended( $ret_val = false );	// Return false by default.
		}

		/**
		 * Since WPSSO Core v8.18.0.
		 *
		 * Returns the first taxonomy term ID, , or false if a term for the $tax_slug is not found.
		 */
		public function get_default_term_id( array $mod, $tax_slug = 'category' ) {

			return self::must_be_extended( $ret_val = false );	// Return false by default.
		}

		/**
		 * Since WPSSO Core v8.16.0.
		 *
		 * Returns an associative array of term IDs and their names or objects.
		 *
		 * The primary or default term ID will be included as the first array element.
		 */
		public function get_primary_terms( array $mod, $tax_slug = 'category', $output = 'objects' ) {

			return self::must_be_extended( $ret_val = array() );
		}

		/**
		 * Since WPSSO Core v8.4.0.
		 *
		 * Always call this method as static::get_meta(), and not self::get_meta(), to execute the method via the child
		 * class instead of the parent class. This method can also be called via $mod[ 'obj' ]::get_meta().
		 */
		public static function get_meta( $mod_id, $meta_key, $single = false ) {

			$ret_val = $single ? '' : array();

			return self::must_be_extended( $ret_val );
		}

		/**
		 * Since WPSSO Core v8.4.0.
		 *
		 * Always call this method as static::update_meta(), and not self::update_meta(), to execute the method via the
		 * child class instead of the parent class. This method can also be called via $mod[ 'obj' ]::update_meta().
		 */
		public static function update_meta( $mod_id, $meta_key, $value ) {

			return self::must_be_extended( $ret_val = false ); // No update.
		}

		/**
		 * Since WPSSO Core v8.4.0.
		 *
		 * Always call this method as staticdelete_meta(), and not selfdelete_meta(), to execute the method via the child
		 * class instead of the parent class. This method can also be called via $mod[ 'obj' ]delete_meta().
		 */
		public static function delete_meta( $mod_id, $meta_key ) {

			return self::must_be_extended( $ret_val = false ); // No delete.
		}

		/**
		 * Since WPSSO Core v8.12.0.
		 *
		 * Used by several SEO integration modules.
		 */
		public static function get_mod_meta( $mod, $meta_key, $single = false, $delete = false ) {

			$ret_val = $not_found = $single ? '' : array();	// Default if no metadata found.

			if ( $meta_key ) {	// Just in case.

				if ( ! empty( $mod[ 'id' ] ) ) {	// Just in case.

					if ( ! empty( $mod[ 'is_post' ] ) ) {

						$ret_val = get_post_meta( $mod[ 'id' ], $meta_key, $single );

						if ( $delete && $ret_val !== $not_found ) {	// Only delete if we have something to delete.

							delete_post_meta( $mod[ 'id' ], $meta_key );
						}

					} elseif ( ! empty( $mod[ 'is_term' ] ) ) {

						$ret_val = get_term_meta( $mod[ 'id' ], $meta_key, $single );

						if ( $delete && $ret_val !== $not_found ) {	// Only delete if we have something to delete.

							delete_term_meta( $mod[ 'id' ], $meta_key );
						}

					} elseif ( ! empty( $mod[ 'is_user' ] ) ) {

						$ret_val = get_user_meta( $mod[ 'id' ], $meta_key, $single );

						if ( $delete && $ret_val !== $not_found ) {	// Only delete if we have something to delete.

							delete_user_meta( $mod[ 'id' ], $meta_key );
						}
					}

					return $ret_val;
				}
			}

			return $ret_val;
		}

		protected static function must_be_extended( $method, $ret_val = null ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->log( 'method must be extended', $class_seq = 2, $func_seq = 2 );
			}

			return $ret_val;
		}
	}
}
