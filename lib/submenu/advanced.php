<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2021 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoSubmenuAdvanced' ) && class_exists( 'WpssoAdmin' ) ) {

	class WpssoSubmenuAdvanced extends WpssoAdmin {

		public function __construct( &$plugin, $id, $name, $lib, $ext ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$this->menu_id   = $id;
			$this->menu_name = $name;
			$this->menu_lib  = $lib;
			$this->menu_ext  = $ext;
		}

		/**
		 * Called by the extended WpssoAdmin class.
		 */
		protected function add_meta_boxes() {

			foreach ( array(
				'plugin'         => _x( 'Advanced Settings', 'metabox title', 'wpsso' ),
				'services'       => _x( 'Service APIs', 'metabox title', 'wpsso' ),
				'contact_fields' => _x( 'Contact Fields', 'metabox title', 'wpsso' ),
				'metadata'       => _x( 'Metadata', 'metabox title', 'wpsso' ),
				'head_tags'      => _x( 'HTML Tags', 'metabox title', 'wpsso' ),
			) as $metabox_id => $metabox_title ) {

				$metabox_screen  = $this->pagehook;
				$metabox_context = 'normal';
				$metabox_prio    = 'default';
				$callback_args   = array(	// Second argument passed to the callback function / method.
				);

				add_meta_box( $this->pagehook . '_' . $metabox_id, $metabox_title,
					array( $this, 'show_metabox_' . $metabox_id ), $metabox_screen,
						$metabox_context, $metabox_prio, $callback_args );
			}
		}

		public function show_metabox_plugin() {

			$metabox_id = 'plugin';
			$table_rows = array();

			$tabs = apply_filters( 'wpsso_advanced_' . $metabox_id . '_tabs', array(
				'settings'     => _x( 'Plugin Admin', 'metabox tab', 'wpsso' ),
				'interface'    => _x( 'Interface', 'metabox tab', 'wpsso' ),
				'integration'  => _x( 'Integration', 'metabox tab', 'wpsso' ),
				'cache'        => _x( 'Caching', 'metabox tab', 'wpsso' ),
			) );

			foreach ( $tabs as $tab_key => $title ) {

				$filter_name = 'wpsso_' . $metabox_id . '_' . $tab_key . '_rows';

				$table_rows[ $tab_key ] = array_merge(
					$this->get_table_rows( $metabox_id, $tab_key ),
					(array) apply_filters( $filter_name, array(), $this->form, $network = false )
				);
			}

			$this->p->util->metabox->do_tabbed( $metabox_id, $tabs, $table_rows );
		}

		public function show_metabox_services() {

			$metabox_id = 'services';
			$table_rows = array();

			$tabs = apply_filters( 'wpsso_advanced_' . $metabox_id . '_tabs', array(
				'media'           => _x( 'Media Services', 'metabox tab', 'wpsso' ),
				'shortening'      => _x( 'Shortening Services', 'metabox tab', 'wpsso' ),
				'ratings_reviews' => _x( 'Ratings and Reviews', 'metabox tab', 'wpsso' ),
			) );

			foreach ( $tabs as $tab_key => $title ) {

				$filter_name = 'wpsso_' . $metabox_id . '_' . $tab_key . '_rows';

				$table_rows[ $tab_key ] = array_merge(
					$this->get_table_rows( $metabox_id, $tab_key ),
					(array) apply_filters( $filter_name, array(), $this->form, $network = false )
				);
			}

			$this->p->util->metabox->do_tabbed( $metabox_id, $tabs, $table_rows );
		}

		public function show_metabox_metadata() {

			$metabox_id = 'metadata';
			$table_rows = array();

			$tabs = apply_filters( 'wpsso_advanced_' . $metabox_id . '_tabs', array(
				'product_attrs' => _x( 'Product Attributes', 'metabox tab', 'wpsso' ),
				'custom_fields' => _x( 'Custom Fields', 'metabox tab', 'wpsso' ),
			) );

			foreach ( $tabs as $tab_key => $title ) {

				$filter_name = 'wpsso_' . $metabox_id . '_' . $tab_key . '_rows';

				$table_rows[ $tab_key ] = array_merge(
					$this->get_table_rows( $metabox_id, $tab_key ),
					(array) apply_filters( $filter_name, array(), $this->form, $network = false )
				);
			}

			$this->p->util->metabox->do_tabbed( $metabox_id, $tabs, $table_rows );
		}

		public function show_metabox_contact_fields() {

			/**
			 * Translate contact method field labels for current language.
			 */
			SucomUtil::transl_key_values( '/^plugin_(cm_.*_label|.*_prefix)$/', $this->p->options, 'wpsso' );

			$metabox_id = 'cm';
			$table_rows = array();
			$info_msg   = $this->p->msgs->get( 'info-' . $metabox_id );

			$tabs = apply_filters( 'wpsso_advanced_' . $metabox_id . '_tabs', array(
				'custom_contacts'  => _x( 'Custom Contacts', 'metabox tab', 'wpsso' ),
				'default_contacts' => _x( 'Default Contacts', 'metabox tab', 'wpsso' ),
			) );

			foreach ( $tabs as $tab_key => $title ) {

				$filter_name = 'wpsso_' . $metabox_id . '_' . $tab_key . '_rows';

				$table_rows[ $tab_key ] = array_merge(
					$this->get_table_rows( $metabox_id, $tab_key ),
					(array) apply_filters( $filter_name, array(), $this->form, $network = false )
				);
			}

			$this->p->util->metabox->do_table( array( '<td>' . $info_msg . '</td>' ), $class_href_key = 'metabox-info metabox-' . $metabox_id . '-info' );

			$this->p->util->metabox->do_tabbed( $metabox_id, $tabs, $table_rows );
		}

		public function show_metabox_head_tags() {

			$metabox_id = 'head_tags';

			$tabs = apply_filters( 'wpsso_advanced_' . $metabox_id . '_tabs', array(
				'facebook'   => _x( 'Facebook', 'metabox tab', 'wpsso' ),
				'open_graph' => _x( 'Open Graph', 'metabox tab', 'wpsso' ),
				'twitter'    => _x( 'Twitter', 'metabox tab', 'wpsso' ),
				'schema'     => _x( 'Schema', 'metabox tab', 'wpsso' ),
				'seo_other'  => _x( 'SEO / Other', 'metabox tab', 'wpsso' ),
			) );

			$table_rows = array();
			$info_msg   = $this->p->msgs->get( 'info-' . $metabox_id );

			foreach ( $tabs as $tab_key => $title ) {

				$filter_name = 'wpsso_' . $metabox_id . '_' . $tab_key . '_rows';

				$table_rows[ $tab_key ] = array_merge(
					$this->get_table_rows( $metabox_id, $tab_key ),
					(array) apply_filters( $filter_name, array(), $this->form, $network = false )
				);
			}

			$this->p->util->metabox->do_table( array( '<td>' . $info_msg . '</td>' ), $class_href_key = 'metabox-info metabox-' . $metabox_id . '-info' );

			$this->p->util->metabox->do_tabbed( $metabox_id, $tabs, $table_rows );
		}

		protected function get_table_rows( $metabox_id, $tab_key ) {

			$table_rows = array();

			switch ( $metabox_id . '-' . $tab_key ) {

				/**
				 * Advanced Settings metabox.
				 */
				case 'plugin-settings':

					$this->add_advanced_plugin_settings_table_rows( $table_rows, $this->form );

					break;
			}

			return $table_rows;
		}
	}
}
