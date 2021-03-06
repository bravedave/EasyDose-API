<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * styleguide : https://codeguide.co/
 *
 * security: admin only
*/

class invoices extends Controller {

	protected function postHandler() {
		$action = $this->getPost('action');

		if ( 'apply-discount' == $action) {
			if ( currentUser::isAdmin()) {
				if ( $id = (int)$this->getPost('invoice_id')) {
					$a = [
						'discount_reason' => (string)$this->getPost('reason'),
						'discount' => (float)$this->getPost('discount'),
						'updated' => \db::dbTimeStamp()
					];

					$dao = new dao\invoices;
					$dao->UpdateByID( $a, $id);

					\Json::ack( $action);

				} else { \Json::nak( $action); }

			} else { \Json::nak( $action); }

		}
		elseif ( 'create invoice' == $action) {
			if ( $user_id = (int)$this->getPost('user_id')) {
				if ( $product_id = (int)$this->getPost('product_id')) {

					$productsDAO = new dao\products;
					$usersDAO = new dao\users;

					if ( $usersDTO = $usersDAO->getByID( $user_id)) {

						// sys::dump( $this->getPost());
						if ( $productDTO = $productsDAO->getByID( $product_id)) {

							$aInvoices = [
								'user_id' => $usersDTO->id,
								'created' => \db::dbTimeStamp(),
								'updated' => \db::dbTimeStamp()
							];

							$aInvoicesDetail = [];
							$aInvoicesDetail[] = [
								'user_id' => $usersDTO->id,
								'invoices_id' => 0,
								'product_id' => $productDTO->id,
								'rate' => $productDTO->rate,
								'created' => \db::dbTimeStamp(),
								'updated' => \db::dbTimeStamp()
							];

							if ( $workstation_id = (int)$this->getPost('workstation_id')) {
								if ( $wksDTO = $productsDAO->getByID( $workstation_id)) {
									$aInvoicesDetail[] = [
										'user_id' => $usersDTO->id,
										'invoices_id' => 0,
										'product_id' => $wksDTO->id,
										'rate' => $wksDTO->rate,
										'created' => \db::dbTimeStamp(),
										'updated' => \db::dbTimeStamp()
									];

								} else { throw new \Exceptions\InvalidWorkstationProduct; }

							}

							if ( count($aInvoicesDetail)) {
								$dao = new dao\invoices;
								$invID = $dao->Insert( $aInvoices);

								$dao = new dao\invoices_detail;
								foreach ($aInvoicesDetail as $line) {
									$line['invoices_id'] = $invID;
									$dao->Insert( $line);

								}

								Response::redirect( url::tostring('account/invoice/' . $invID), 'created invoice');

							} else { throw new \Exceptions\FailedToCreateInvoice; }

						} else { throw new \Exceptions\ProductNotFound; }

					} else { throw new \Exceptions\InvalidUser; }

				} else { throw new \Exceptions\InvalidProduct; }

			} else { throw new \Exceptions\MissingUserID; }

		}
		elseif ( 'license-exclusion' == $action) {
			if ( currentUser::isAdmin()) {
				if ( $id = (int)$this->getPost('invoice_id')) {
					$a = [
						'license_exclusion' => (int)$this->getPost('license_exclusion'),
						'updated' => \db::dbTimeStamp()
					];

					$dao = new dao\invoices;
					$dao->UpdateByID( $a, $id);

					\Json::ack( $action);

				} else { \Json::nak( $action); }

			} else { \Json::nak( $action); }

		}
		elseif ( 'make-authoritative' == $action) {
			if ( currentUser::isAdmin()) {
				if ( $id = (int)$this->getPost('invoice_id')) {
					$a = [
						'authoritative' => (int)$this->getPost('value'),
						'updated' => \db::dbTimeStamp()
					];

					$dao = new dao\invoices;
					$dao->UpdateByID( $a, $id);

					\Json::ack( $action);

				} else { \Json::nak( $action); }

			} else { \Json::nak( $action); }

		}
		elseif ( 'update-expires' == $action) {
			if ( currentUser::isAdmin()) {
				if ( $id = (int)$this->getPost('invoice_id')) {
					$a = [
						'expires' => $this->getPost('expires'),
						'updated' => \db::dbTimeStamp()
					];

					$dao = new dao\invoices;
					$dao->UpdateByID( $a, $id);

					\Json::ack( $action);

				} else { \Json::nak( $action); }

			} else { \Json::nak( $action); }

		}
		elseif ( 'update-workstation_override' == $action) {
			if ( currentUser::isAdmin()) {
				if ( $id = (int)$this->getPost('invoice_id')) {
					$a = [
						'workstation_override' => $this->getPost('workstations'),
						'updated' => \db::dbTimeStamp()
					];

					$dao = new dao\invoices;
					$dao->UpdateByID( $a, $id);

					\Json::ack( $action);

				} else { \Json::nak( $action); }

			} else { \Json::nak( $action); }

		}
		elseif ( 'update-state' == $action) {
			if ( currentUser::isAdmin()) {
				if ( $id = (int)$this->getPost('invoice_id')) {
					$a = [
						'state' => $this->getPost('state'),
						'state_change' => 'manual',
						'state_changed' => \db::dbTimeStamp(),
						'state_changed_by' => currentUser::id(),
						'updated' => \db::dbTimeStamp()

					];

					$dao = new dao\invoices;
					$dao->UpdateByID( $a, $id);

					\Json::ack( $action);

				} else { \Json::nak( $action); }

			} else { \Json::nak( $action); }

		}

	}

	protected function _index() {
		if ( currentUser::isAdmin()) {
			$dao = new dao\invoices;
			$this->data = (object)[ 'invoices' => $dao->getAll() ];

			$this->render([
				'title' => $this->title = 'invoices',
				'primary' => 'list',
				'secondary' => 'main-index'

			]);

		} else { throw new \exceptions\AccessViolation; }

	}

}
