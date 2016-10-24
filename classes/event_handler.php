<?php

/**
 * Prevents guests from accessing certain controller actions.
 *
 * @author Luca Vicidomini <lvicidomini@unisa.it>
 */
class SPODPRIVACY_CLASS_EventHandler
{
    /**
     * Singleton instance.
     *
     * @var SPODPRIVACY_CLASS_EventHandler
     */
    private static $classInstance;

	/**
	 * Locked-down paths.
	 *
	 * @var array
	 */
	private $locked = [];

	/**
	 * Whether this plug-in is in debug mode.
	 *
	 * @var bool
	 */
	private $debug;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return SPODPRIVACY_CLASS_EventHandler
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct()
    {
	    $preference = BOL_PreferenceService::getInstance()->findPreference('spodprivacy_filter');
	    $filter = empty($preference) ? "" : $preference->defaultValue;
	    $this->locked = json_decode( $filter, true );

	    $preference = BOL_PreferenceService::getInstance()->findPreference('spodprivacy_debug');
	    $debug = empty($preference) ? false : $preference->defaultValue;
	    $this->debug = $debug;
    }

	/**
	 * OW_EventManager::ON_AFTER_ROUTE listener
	 *
	 * @param $event Event
	 */
    public function onAfterRoute( $event )
    {
    	// Print Controller and Action for this route
    	if ( $this->debug )
	    {
		    $this->printDebug();
	    }

    	// User authenticated, do nothing
    	if ( OW::getUser()->isAuthenticated() )
	    {
	    	return;
	    }

    	$route = OW::getRouter()->getUsedRoute();

	    // 404
	    if ( ! $route )
	    {
		    return;
	    }

	    $routeAttrs = $route->getDispatchAttrs();
	    $controller = $routeAttrs['controller'];
	    $action = $routeAttrs['action'];

	    // Check if the route is locked
	    if ( isset( $this->locked[ $controller ] ) )
	    {
	    	$lockedActions = explode( ',', $this->locked[ $controller ] );
		    if ( in_array( '*', $lockedActions ) || in_array( $action, $lockedActions ) )
		    {
			    // Since at this point the AuthenticationException event handler
			    // is not bound, we manually perform the redirection
			    $ae = new AuthenticateException();
			    $uri = $ae->getUrl();
			    OW::getApplication()->redirect( $uri );
			    return;
		    }
	    } // if ( isset( $this->locked[ $controller ] ) )
    }

	/**
	 * Prints current Controller and Action. Useful when the administrators
	 * needs to build a filter for the plug-in.
	 */
    protected function printDebug()
    {
	    $route = OW::getRouter()->getUsedRoute();

	    // 404
	    if ( ! $route )
	    {
	    	return;
	    }

	    $routeAttrs = $route->getDispatchAttrs();
	    echo '
	            <div class="ow_debug_cont">
	                <div class="ow_debug_body">
	                    <div class="ow_debug_cap vardump">OW Debug - SPOD Privacy</div>
	                    <table>
	                        <tr>
	                            <td class="lbl">Controller:</td>
	                            <td class="cnt">' . $routeAttrs['controller'] . '</td>
	                        </tr>
	                        <tr>
	                            <td class="lbl">Action:</td>
	                            <td class="cnt">' . $routeAttrs['action'] . '</td>
	                        </tr>
	                    </table>
	                </div>
	            </div>
	            ';
    }


}