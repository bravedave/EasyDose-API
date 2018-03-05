<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

Namespace dao;

class invoices_detail extends _dao {
	protected $_db_name = 'invoices_detail';

	public function getLines( \dao\dto\invoices $dto) {
		$_sql = sprintf( 'SELECT
		 id.id,
		 id.user_id,
		 id.invoices_id,
		 id.product_id,
		 id.rate,
		 p.name,
		 p.description,
		 p.term
		 FROM invoices_detail id
		 	LEFT JOIN products p ON p.id = id.product_id
		 WHERE
		  id.invoices_id = %s', $dto->id);

		if ( $res = $this->Result( $_sql))
			return ( $res->dtoSet());

		return ( FALSE);

	}

}
