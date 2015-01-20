<?php
/**
 * Multi Device Switcher Command
 */
class Multi_Device_Switcher_Command extends WP_CLI_Command {

	private $options = 'multi_device_switcher_options';

	/**
	 * get status of settings
	 *
	 * ## EXAMPLES
	 *
	 *     wp multi-device status
	 *
	*/
	public function status( $args, $assoc_args ) {
		$options = get_option( $this->options );
		$rows = array();

		$slug_table = array( 'None' => '' );
		$themes = wp_get_themes();
		foreach ( $themes as $theme_slug => $header ) {
			$slug_table[ $header->get( 'Name' ) ] = $theme_slug;
		}

		$rows[] = array(
			'Device' => 'smartphone (Smart Phone)',
			'Theme' => $options['theme_smartphone'],
			'Slug' => $slug_table[ $options['theme_smartphone'] ],
			'UserAgent' => $options['userAgent_smart'],
		);
		$rows[] = array(
			'Device' => 'tablet (Tablet PC)',
			'Theme' => $options['theme_tablet'],
			'Slug' => $slug_table[ $options['theme_tablet'] ],
			'UserAgent' => $options['userAgent_tablet'],
		);
		$rows[] = array(
			'Device' => 'mobile (Mobile Phone)',
			'Theme' => $options['theme_mobile'],
			'Slug' => $slug_table[ $options['theme_mobile'] ],
			'UserAgent' => $options['userAgent_mobile'],
		);
		$rows[] = array(
			'Device' => 'game (Game Platforms)',
			'Theme' => $options['theme_game'],
			'Slug' => $slug_table[ $options['theme_game'] ],
			'UserAgent' => $options['userAgent_game'],
		);

		foreach ( $options as $custom_switcher_option => $custom_switcher_theme ) {
			if ( ! preg_match( '/^custom_switcher_theme_/', $custom_switcher_option ) ) {
				continue;
			}

			$custom_switcher_name = preg_replace( '/^custom_switcher_theme_/', '', $custom_switcher_option );

			$rows[] = array(
				'Device' => $custom_switcher_name,
				'Theme' => $options[ 'custom_switcher_theme_' . $custom_switcher_name ],
				'Slug' => $slug_table[ $options[ 'custom_switcher_theme_' . $custom_switcher_name ] ],
				'UserAgent' => $options[ 'custom_switcher_userAgent_' . $custom_switcher_name ],
			);
		}

		$default_theme = wp_get_theme()->get( 'Name' );
		$default_theme .= ' | ';
		$default_theme .= get_stylesheet();
		WP_CLI::line( 'Active Theme: ' . $default_theme );

		WP_CLI\Utils\format_items( 'table', $rows, array( 'Device', 'Theme', 'Slug', 'UserAgent' ) );

		$line = '';
		$line .= 'PC Switcher: ';
		$line .= $options['pc_switcher'] ? 'on' : 'off';
		$line .= "\n";
		$line .= 'default CSS: ';
		$line .= $options['default_css'] ? 'on' : 'off';

		WP_CLI::line( $line );
	}

	/**
	 * get or switch a theme
	 *
	 * ## OPTIONS
	 *
	 * <device>
	 * : The name of device or Custom Switcher
	 *
	 * [<slug>]
	 * : The slug of theme
	 * input 'None', if you want to without theme
	 *
	 * [--theme=<theme>]
	 * : The name of theme
	 * input 'None', if you want to without theme
	 *
	 * ## EXAMPLES
	 *
	 *     # get a theme|slug of smartphone
	 *     wp multi-device theme smartphone
	 *
	 *     # switch twentyfifteen in theme of smartphone using theme slug
	 *     wp multi-device theme smartphone twentyfifteen
	 *
	 *     # switch twentyfifteen in theme of smartphone using theme argument
	 *     wp multi-device theme smartphone --theme='Twenty Fifteen'
	 *
	 * @synopsis <device> [<slug>] [--theme=<theme>]
	*/
	public function theme( $args, $assoc_args ) {
		$name = isset( $args[0] ) ? $args[0] : null;
		$slug = isset( $args[1] ) ? $args[1] : null;
		$theme = isset( $assoc_args['theme'] ) ? $assoc_args['theme'] : null;

		$options = get_option( $this->options );

		if ( isset( $slug ) ) {
			if ( '' == $slug || 'None' == $slug ) {
				$theme = 'None';
			}
		}

		$slug_table = array( 'None' => '' );
		$themes = wp_get_themes();
		foreach ( $themes as $theme_slug => $header ) {
			$slug_table[ $header->get( 'Name' ) ] = $theme_slug;
			if ( $slug == $theme_slug ) {
				$theme = $header->get( 'Name' );
			}
		}

		if ( isset( $theme ) ) {
			$default_theme = wp_get_theme()->get( 'Name' );
			if ( $default_theme == $theme ) {
				WP_CLI::error( $theme . ' theme is in active' );
			}

			if ( ! isset( $slug_table[ $theme ] ) ) {
				WP_CLI::error( $theme . ' theme is not installed' );
			}

			if ( in_array( $name, array( 'smartphone', 'smart', 'tablet', 'mobile', 'game' ) ) ) {
				if ( 'smart' == $name ) {
					$name = 'smartphone';
				}
				$options[ 'theme_' . $name ] = $theme;

				update_option( $this->options, $options );
				WP_CLI::success( 'switch ' . $name . ' theme to ' . $theme );
			}
			else if ( isset( $options[ 'custom_switcher_theme_' . $name ] ) ) {
				$options[ 'custom_switcher_theme_' . $name ] = $theme;

				update_option( $this->options, $options );
				WP_CLI::success( 'switch ' . $name . ' theme to ' . $theme );
			}
			else {
				WP_CLI::error( $name . ' don\'t exist' );
			}
		}
		else {
			if ( in_array( $name, array( 'smartphone', 'smart', 'tablet', 'mobile', 'game' ) ) ) {
				if ( 'smart' == $name ) {
					$name = 'smartphone';
				}
				WP_CLI::success( $options[ 'theme_' . $name ] . ' | ' . $slug_table[ $options[ 'theme_' . $name ] ] );
			}
			else if ( isset( $options[ 'custom_switcher_theme_' . $name ] ) ) {
				WP_CLI::success( $options[ 'custom_switcher_theme_' . $name ] . ' | ' . $slug_table[ $options[ 'custom_switcher_theme_' . $name ] ] );
			}
			else {
				WP_CLI::error( $name . ' don\'t exist' );
			}
		}
	}

	/**
	 * get or set UserAgent
	 *
	 * ## OPTIONS
	 *
	 * <device>
	 * : The name of device or Custom Switcher
	 *
	 * [<UserAgent>]
	 * : UserAgent
	 * Comma-separated values (csv) format
	 *
	 * ## EXAMPLES
	 *
	 *     # get UserAgent of tablet
	 *     wp multi-device useragent tablet
	 *
	 *     # set UserAgent in theme of tablet
	 *     wp multi-device useragent tablet 'iPad, Kindle, Sony Tablet, Nexus 7'
	 *
	 * @synopsis <device> [<UserAgent>]
	*/
	public function useragent( $args, $assoc_args ) {
		$name = isset( $args[0] ) ? $args[0] : null;
		$useragent = isset( $args[1] ) ? $args[1] : null;

		$options = get_option( $this->options );

		if ( isset( $useragent ) ) {
			if ( in_array( $name, array( 'smartphone', 'smart', 'tablet', 'mobile', 'game' ) ) ) {
				if ( 'smartphone' == $name ) {
					$name = 'smart';
				}
				$options[ 'userAgent_' . $name ] = $useragent;

				update_option( $this->options, $options );
				WP_CLI::success( 'set ' . $name . ' UserAgent to ' . $useragent );
			}
			else if ( isset( $options[ 'custom_switcher_theme_' . $name ] ) ) {
				$options[ 'custom_switcher_userAgent_' . $name ] = $useragent;

				update_option( $this->options, $options );
				WP_CLI::success( 'set ' . $name . ' UserAgent to ' . $useragent );
			}
			else {
				WP_CLI::error( $name . ' don\'t exist' );
			}
		}
		else {
			if ( in_array( $name, array( 'smartphone', 'smart', 'tablet', 'mobile', 'game' ) ) ) {
				if ( 'smartphone' == $name ) {
					$name = 'smart';
				}
				WP_CLI::success( $options[ 'userAgent_' . $name ] );
			}
			else if ( isset( $options[ 'custom_switcher_theme_' . $name ] ) ) {
				WP_CLI::success( $options[ 'custom_switcher_userAgent_' . $name ] );
			}
			else {
				WP_CLI::error( $name . ' don\'t exist' );
			}
		}
	}

	/**
	 * reset Settings to Default UserAgent
	 *
	 * ## EXAMPLES
	 *
	 *     wp multi-device reset
	 *
	 * @synopsis
	*/
	public function reset( $args, $assoc_args ) {
		$options = get_option( $this->options );
		$default_options = multi_device_switcher_get_default_options();

		$options['userAgent_smart'] = $default_options['userAgent_smart'];
		$options['userAgent_tablet'] = $default_options['userAgent_tablet'];
		$options['userAgent_mobile'] = $default_options['userAgent_mobile'];
		$options['userAgent_game'] = $default_options['userAgent_game'];

		update_option( $this->options, $options );
		WP_CLI::success( 'reset Settings to Default UserAgent' );
	}

	/**
	 * add Custom Switcher
	 *
	 * ## OPTIONS
	 *
	 * <device>
	 * : The name of Custom Switcher
	 * 20 characters max, alphanumeric
	 *
	 * [<slug>]
	 * : The slug of theme
	 *
	 * [<UserAgent>]
	 * : UserAgent
	 * Comma-separated values (csv) format
	 *
	 * [--theme=<theme>]
	 * : The name of theme
	 *
	 * ## EXAMPLES
	 *
	 *     # add example Custom Switcher
	 *     wp multi-device add example
	 *
	 *     # add example Custom Switcher. set twentyfifteen theme and UserAgent using theme slug
	 *     wp multi-device add example twentyfifteen 'iPad, Kindle, Sony Tablet, Nexus 7'
	 *
	 *     # add example Custom Switcher. set twentyfifteen theme and UserAgent using theme argument
	 *     wp multi-device add example --theme='Twenty Fifteen'
	 *
	 * @synopsis <device> [<slug>] [<UserAgent>] [--theme=<theme>]
	*/

	public function add( $args, $assoc_args ) {
		$name = isset( $args[0] ) ? $args[0] : null;
		$slug = isset( $args[1] ) ? $args[1] : null;
		$useragent = isset( $args[2] ) ? $args[2] : null;
		$theme = isset( $assoc_args['theme'] ) ? $assoc_args['theme'] : null;

		if ( ! preg_match( '/^[A-Za-z0-9]{1,20}$/', $name ) ) {
			WP_CLI::error( '20 characters max, alphanumeric' );
		}

		$slug_table = array( 'None' => '' );
		$themes = wp_get_themes();
		foreach ( $themes as $theme_slug => $header ) {
			$slug_table[ $header->get( 'Name' ) ] = $theme_slug;
			if ( ! isset( $assoc_args['theme'] ) && $slug == $theme_slug ) {
				$theme = $header->get( 'Name' );
			}
		}

		$options = get_option( $this->options );
		if ( in_array( $name, array( 'smartphone', 'smart', 'tablet', 'mobile', 'game' ) ) ) {
			WP_CLI::error( 'can\'t add Default Switcher' );
		}
		else if ( isset( $options[ 'custom_switcher_theme_' . $name ] ) ) {
			WP_CLI::error( 'Custom Switcher already exists' );
		}
		else {
			$default_theme = wp_get_theme()->get( 'Name' );
			if ( $default_theme == $theme ) {
				WP_CLI::error( $theme . ' theme is in active' );
			}

			if ( isset( $slug ) || isset( $assoc_args['theme'] ) ) {
				if ( isset( $slug_table[ $theme ] ) ) {
					$options[ 'custom_switcher_theme_' . $name ] = $theme;
				}
				else {
					WP_CLI::error( $theme . ' theme are not installed' );
				}
			}
			else {
				$options[ 'custom_switcher_theme_' . $name ] = 'None';
			}

			$options[ 'custom_switcher_userAgent_' . $name ] = isset( $useragent ) ? $useragent : '';

			update_option( $this->options, $options );
			WP_CLI::success( 'add ' . $name . ' Custom Switcher' );
		}
	}

	/**
	 * delete Custom Switcher
	 *
	 * ## OPTIONS
	 *
	 * <device>
	 * : The name of Custom Switcher
	 *
	 * ## EXAMPLES
	 *
	 *     # delete example Custom Switcher
	 *     wp multi-device delete example
	 *
	 * @synopsis <device>
	*/
	public function delete( $args, $assoc_args ) {
		$name = isset( $args[0] ) ? $args[0] : null;

		$options = get_option( $this->options );

		if ( in_array( $name, array( 'smartphone', 'smart', 'tablet', 'mobile', 'game' ) ) ) {
			WP_CLI::error( 'Default Switcher can\'t delete' );
		}
		else if ( isset( $options[ 'custom_switcher_theme_' . $name ] ) ) {
			unset( $options[ 'custom_switcher_theme_' . $name ] );
			unset( $options[ 'custom_switcher_userAgent_' . $name ] );

			update_option( $this->options, $options );
			WP_CLI::success( 'delete ' . $name . ' Custom Switcher' );
		}
		else {
			WP_CLI::error( 'Custom Switcher don\'t exist' );
		}
	}

	/**
	 * turn on/off PC Switcher
	 *
	 * ## OPTIONS
	 *
	 * <flag>
	 * : on
	 * off
	 *
	 * ## EXAMPLES
	 *
	 *     # turn on PC Switcher
	 *     wp multi-device pc-switcher on
	 *
	 *     # turn off PC Switcher
	 *     wp multi-device pc-switcher off
	 *
	 * @synopsis <flag>
	 * @subcommand pc-switcher
	*/
	public function pc_switcher( $args, $assoc_args ) {
		$flag = isset( $args[0] ) ? $args[0] : null;

		$options = get_option( $this->options );

		if ( 'on' == $flag ) {
			$options['pc_switcher'] = 1;
			update_option( $this->options, $options );
			WP_CLI::success( 'turn on PC Switcher' );
		}
		else if ( 'off' == $flag ) {
			$options['pc_switcher'] = 0;
			update_option( $this->options, $options );
			WP_CLI::success( 'turn off PC Switcher' );
		}
		else {
			WP_CLI::error( 'Invalid flag' );
		}
	}

	/**
	 * turn on/off default CSS
	 *
	 * ## OPTIONS
	 *
	 * <flag>
	 * : on
	 * off
	 *
	 * ## EXAMPLES
	 *
	 *     # turn on default CSS
	 *     wp multi-device css on
	 *
	 *     # turn off default CSS
	 *     wp multi-device css off
	 *
	 * @synopsis <flag>
	*/
	public function css( $args, $assoc_args ) {
		$flag = isset( $args[0] ) ? $args[0] : null;

		$options = get_option( $this->options );

		if ( 'on' == $flag ) {
			$options['default_css'] = 1;
			update_option( $this->options, $options );
			WP_CLI::success( 'turn on default CSS' );
		}
		else if ( 'off' == $flag ) {
			$options['default_css'] = 0;
			update_option( $this->options, $options );
			WP_CLI::success( 'turn off default CSS' );
		}
		else {
			WP_CLI::error( 'Invalid flag' );
		}
	}
}

WP_CLI::add_command( 'multi-device', 'Multi_Device_Switcher_Command' );
