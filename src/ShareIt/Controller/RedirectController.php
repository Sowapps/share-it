<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 */

namespace ShareIt\Controller;

use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPResponse;
use Orpheus\InputController\HTTPController\RedirectHTTPResponse;
use ShareIt\User;

class RedirectController extends HTTPController {
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 */
	public function run($request) {
		
		return new RedirectHTTPResponse(u(User::isLogged() ? DEFAULTMEMBERROUTE : DEFAULT_ROUTE));
	}
	
	
}
