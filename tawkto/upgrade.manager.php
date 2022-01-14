<?php

require_once dirname( __FILE__ ) . '/upgrades/version.070.php';

/**
 * Upgrade manager for tawk.to plugin
 */
class TawkToUpgradeManager {
	/**
	 * $upgrades
	 *
	 * @var array
	 */
	protected $upgrades;
	/**
	 * $prev_ver
	 *
	 * @var string
	 */
	protected $prev_ver;
	/**
	 * $curr_ver
	 *
	 * @var string
	 */
	protected $curr_ver;
	/**
	 * $version_var_name
	 *
	 * @var string
	 */
	protected $version_var_name;

	/**
	 * Constructor
	 *
	 * @param string $version          Plugin version.
	 * @param string $version_var_name Version option variable name.
	 */
	public function __construct( $version, $version_var_name ) {
		$this->upgrades = array(
			TawkToUpgradeVersion070::get_version() => TawkToUpgradeVersion070::class,
		);

		$this->version_var_name = $version_var_name;
		$this->curr_ver         = $version;
		$this->prev_ver         = get_option( $version_var_name, '' );
	}

	/**
	 * Start doing upgrades
	 */
	public function start() {
		if ( ! empty( $this->prev_ver ) && version_compare( $this->prev_ver, $this->curr_ver ) >= 0 ) {
			// do not do anything.
			return;
		}

		// special case: we've never set the version before.
		// All plugins prior to the current version needs the upgrade.
		if ( version_compare( $this->prev_ver, $this->curr_ver ) < 0 ) {
			// are there upgrade steps depending on how out-of-date?
			foreach ( $this->upgrades as $upgrade_ver => $upgrade ) {
				// only run upgrades if upgrade version is lower than
				// and equal to the current version.
				if ( version_compare( $upgrade_ver, $this->curr_ver ) <= 0 ) {
					$this->do_upgrade( $upgrade_ver );
				}

				update_option( $this->version_var_name, $upgrade_ver );
			}
		}

	}

	/**
	 * Gets upgrade class by provided version
	 *
	 * @param string $version Upgrade version.
	 *
	 * @return string|null Returns `upgrade class name` if version exists in the list. Otherwise, returns `null`.
	 */
	protected function get_upgrade_class( $version ) {
		if ( false === array_key_exists( $version, $this->upgrades ) ) {
			return null;
		}

		return $this->upgrades[ $version ];
	}

	/**
	 * Does the version upgrade depending on the provided plugin version.
	 *
	 * @param  string $version Plugin version.
	 * @return void
	 */
	protected function do_upgrade( $version ) {
		$upgrade_class = $this->get_upgrade_class( $version );

		if ( true === is_null( $upgrade_class ) ) {
			return;
		}

		$upgrade_class::upgrade();
	}

	/**
	 * Registers hooks for upgrade.
	 */
	public function register_hooks() {
		add_action( 'plugins_loaded', array( $this, 'start' ) );
	}
}
