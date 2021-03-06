<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\sqlite;

$dao = new \dao\users;
$dao->check();

if ( $res = $this->db->Result( 'SELECT count(*) count FROM users' )) {
	if ( $dto = $res->dto()) {
		if ( $dto->count < 1 ) {
			$a = [
				'username' => 'admin',
				'name' => 'Administrator',
				'pass' => password_hash( 'admin', PASSWORD_DEFAULT),
				'admin' => 1,
				'created' => \db::dbTimeStamp(),
				'updated' => \db::dbTimeStamp()
				];
			$this->db->Insert( 'users', $a );

			\sys::logger( 'wrote users defaults');

		}
		else {
			\sys::logger( sprintf( 'there are %d user(s)', $dto->count));

		}

	}

}
