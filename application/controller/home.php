<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
class home extends Controller {
	protected $firstRun = FALSE;

	protected function _authorize() {
		/*
		 * curl -X POST -H "Accept: application/json" -d action="-system-logon-" -d u="john" -d p="" "http://localhost/"
		 */
		$action = $this->getPost( 'action');
		if ( $action == '-system-logon-') {
			if ( $u = $this->getPost( 'u')) {
				if ( $p = $this->getPost( 'p')) {
					$dao = new \dao\users;
					if ( $dto = $dao->validate( $u, $p))
						\Json::ack( $action);
					else
						\Json::nak( $action);
					die;

				}

			}

		}
		elseif ( $action == '-send-password-') {
			/*
			 * send a link to reset the password
			 */
		 	\sys::logger('-send-password-link-');
		 	if ( $u = $this->getPost( 'u')) {
				$dao = new \dao\users;
				if ( $dto = $dao->getUserByEmail( $u)) {
					/*
					 * this will only work for email addresses
					 */
				 	if ( $dao->sendResetLink( $dto)) {
						\Json::ack( 'sent reset link')
							->add('message', 'sent link, check your email and your junk mail');
						// \sys::logger('-sent-password-link-');

					}	else {
						\Json::nak( $action);
						// \sys::logger('-did-not-sent-password-link-');

					}

				}	else {
					\Json::nak( $action);
					// \sys::logger('-did-not-sent-password-link-email-not-found');

				}

			}	else { \Json::nak( $action); }
			die;

		}
		throw new dvc\Exceptions\InvalidPostAction;

	}

	protected function authorize() {
		if ( $this->isPost())
			$this->_authorize();
		else
			parent::authorize();

	}

	protected function postHandler() {
		$action = $this->getPost('action');

	}

	function __construct( $rootPath) {
		$this->firstRun = sys::firstRun();

		if ( $this->firstRun)
			$this->RequireValidation = FALSE;
		else
			$this->RequireValidation = \sys::lockdown();

		parent::__construct( $rootPath);

	}

	protected function _index() {
		$p = new page( $this->title = sys::name());
		$p
			->header()
			->title()
			->primary();

		$this->load( 'readme');

		$p->secondary();
			$this->load('main-index');

	}

	public function index( $data = '' ) {
		if ( $this->isPost())
			$this->postHandler();

		elseif ( $this->firstRun)
			$this->dbinfo();

		else
			$this->_index();

	}

	public function dbinfo() {
		$p = new dvc\pages\bootstrap('dbinfo');
			$p
			->header()
			->title()
			->primary();

			$dbinfo = new dao\dbinfo;
			$dbinfo->dump();

		$p->secondary();
			$this->load('main-index');

	}

	public function primo() {

	}

}
