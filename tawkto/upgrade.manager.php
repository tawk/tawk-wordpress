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
			TawkToUpgradeVersion070::get_version(),
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
			foreach ( $this->upgrades as $next_ver ) {
				$this->do_upgrade( $next_ver );
			}
		}

		// update stored plugin version for next time.
		update_option( $this->version_var_name, $this->curr_ver );
	}

	/**
	 * Does the version upgrade depending on the provided plugin version.
	 *
	 * @param  string $version Plugin version.
	 * @return void
	 */
	protected function do_upgrade( $version ) {
		switch ( $version ) {
			case TawkToUpgradeVersion070::get_version():
				TawkToUpgradeVersion070::upgrade();
				break;
		}
	}

	/**
	 * Registers hooks for upgrade.
	 */
	public function register_hooks() {
		add_action( 'plugins_loaded', array( $this, 'start' ) );
	}
}
