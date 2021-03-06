<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * styleguide : https://codeguide.co/
*/

use dvc\html;

class invoice {
	protected $sys, $account, $invoice, $license;

	function __construct( $sys, $account, $invoice, $license) {
		$this->sys = $sys;
		$this->account = $account;
		$this->invoice = $invoice;
		$this->license = $license;

	}

	function render() {

		/*-- -------------[ head of invoice ]------------- -- -
		+---------------------+---------------------+
		|                     | Company Details     |
		+---------------------+---------------------+
		| Customer Details    |                     |
		+---------------------+---------------------+
		--*/

		$thead = new html\table("table table-sm borderless m-0");

		$tr = $thead->tr();

		$path = __DIR__ . '/public/images/logo.jpg';
		$type = pathinfo( $path, PATHINFO_EXTENSION);
		$data = file_get_contents( $path);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode( $data);

		$img = sprintf( '<img src="%s" />', $base64);

		/*--- ---[ start our details]--- ---*/
		$tr->td( $img, ['style' => 'width: 400px; padding: 0']);

		$td = $tr->td( null);

		$td->appendChild( new html\div( $this->sys->name));
		if ( $this->sys->street) {
			$td->appendChild( new html\div( $this->sys->street));

		}

		$_a = [];
		if ( $this->sys->town) $_a[] = $this->sys->town;
		if ( $this->sys->state) $_a[] = $this->sys->state;
		if ( $this->sys->postcode ) $_a[] = $this->sys->postcode;
		if ( count( $_a)) $td->appendChild( new html\div( implode( ' ', $_a)));

		if ( $this->sys->abn) $td->appendChild( new html\div( sprintf('ABN: %s', $this->sys->abn)));

		/*--- ---[ end our details]--- ---*/

		/*--- ---[ start their details]--- ---*/
		$tr = $thead->tr();
		$td = $tr->td( null);
		$tr->td('&nbsp;');

		$td->appendChild( new html\div( $this->account->name));
		if ( $this->account->business_name && $this->account->business_name != $this->account->name) {
			$td->appendChild( new html\div( $this->account->business_name));

		}

		if ( $this->account->street) {
			$td->appendChild( new html\div( $this->account->street));

		}

		$_a = [];
		if ( $this->account->town) $_a[] = $this->account->town;
		if ( $this->account->state) $_a[] = $this->account->state;
		if ( $this->account->postcode ) $_a[] = $this->account->postcode;
		if ( count( $_a)) $td->appendChild( new html\div( implode( ' ', $_a)));
		if ( $this->account->abn) $td->appendChild( new html\div( sprintf('ABN: %s', $this->account->abn)));
		/*--- ---[ end their details]--- ---*/

		/*--- ---[ headline ]--- ---*/
		$headline = new html\table('table table-sm borderless m-0');
		//~ $hcellWith = ( $this->invoice->authoritative ? 25 : 33);
		$hcellWith = 33;

		$status = 'not paid';
		//~ if ( 'provisional' == $this->invoice->state) {
		if ( dao\invoices::isProvisional( $this->invoice)) {
			$status = 'provisional';

			//~ }

			//~ $cDate = new DateTime($this->invoice->created);
			//~ $diff = $cDate->diff( new DateTime());
			//~ if ( $diff->days < \config::provisional_invoice_grace) {
				//~ $status = 'provisional';

			//~ }

		}
		elseif ( 'approved' == $this->invoice->state) {
			$status = 'paid';

		}
		elseif ( 'canceled' == $this->invoice->state) {
			$status = 'canceled';

		}


		$tr = $headline->tr();
		$tr->td( new html\div( sprintf('Tax Invoice: <strong>%s</strong>', sys::format_invoice_number( $this->invoice->id))),
			[ 'class' => 'bx-1', 'style' => sprintf( 'width: %s%%;', $hcellWith)]);
		$tr->td( new html\div( sprintf('Status: <strong>%s</strong>', $status)),
			[ 'class' => 'bx-1 text-center', 'style' => sprintf( 'width: %s%%;', $hcellWith)]);
		$tr->td( new html\div( sprintf('Invoice Date: <strong>%s</strong>', strings::asLocalDate( $this->invoice->created))),
			[ 'class' => 'bx-1 text-right', 'style' => sprintf( 'width: %s%%;', $hcellWith)]);
		//~ $tr->td( new html\div( sprintf('Expires: %s', strings::asLocalDate( $this->invoice->expires))),
			//~ [ 'class' => 'bx-1 text-right', 'style' => 'width: 25%;']);

		$tr = $thead->tr();
		$td = $tr->td( $headline, ['colspan' => '2', 'style' => 'padding: 0;' ]);
		/*--- ---[ headline ]--- ---*/

		/*-- ---[ body of invoice ]---
		+--------------------+----------+----------+
		| Description        | Rate     | Term     |
		+--------------------+----------+----------+
		| EasyDose 10        | 550      | Year     |
		| Discount           |  50      |          |
		+--------------------+----------+----------+
		| Total              | 500      |          |
		| Includes GST       |  49.50   |          |
		+--------------------+----------+----------+
		--*/

		$tbody = new html\table('table');
		$tr = $tbody->head()->tr();
		$tr->td('Description');
		$tr->td('Rate',['class' => 'text-right']);
		// $tr->td('Valid To',['class' => 'text-center']);
		$tr->td('Term',['class' => 'text-center']);

    foreach ( $this->invoice->lines as $dto) {
		$validTo = $this->invoice->expires;

		$tr = $tbody->tr();
		$tr->td( sprintf( '%s<br />%s', $dto->name, $dto->description));
		$tr->td( number_format( $dto->rate, 2), ['class' => 'text-right']);
		// $tr->td( strings::asLocalDate( $validFrom), ['class' => 'text-center']);
		// $tr->td( strings::asLocalDate( $validTo), ['class' => 'text-center']);
		$tr->td( $dto->term, ['class' => 'text-center']);

    }	// foreach ( $this->invoice->lines as $dto)

		if ( $this->invoice->discount) {
			$tr = $tbody->tr();
			$tr->td( sprintf( 'Discount: %s', $this->invoice->discount_reason));
			$tr->td( number_format( $this->invoice->discount * -1, 2), [ 'class' => 'text-right']);
			$tr->td( '&nbsp;');
		}
		$tr = $tbody->tr();
		$tr->td( '<strong>Total:</strong>', [ 'style' => 'border-top: 6px double #dee2e6;']);
		$tr->td( sprintf( '<strong>%s</strong>', number_format( $this->invoice->total, 2)),
			[ 'style' => 'border-top: 6px double #dee2e6;', 'class' => 'text-right']);
		$tr->td( '&nbsp;',[ 'style' => 'border-top: 6px double #dee2e6;']);

		$tr = $tbody->tr();
		$tr->td( 'Total includes GST:');
		$tr->td( number_format( $this->invoice->tax, 2), [ 'class' => 'text-right']);
		$tr->td( '&nbsp;');

		$tlicense = new html\table("table");
		if ( 'active' == $this->license->state) {

			$tr = $tlicense->head()->tr();
			$td = $tr->td( '<h6 class="m-0">current license</h6>', ['colspan' => '4']);

			$tr = $tlicense->head()->tr();
			$td = $tr->td( 'description');
			$td = $tr->td( 'workstation');
			$td = $tr->td( 'expires');
			$td = $tr->td( 'state');

			$tr = $tlicense->tr();
			$td = $tr->td( sprintf( '%s<br />%s', $this->license->description, $this->license->product));
			$td = $tr->td( (string)$this->license->workstations);
			$td = $tr->td( strings::asLocalDate( $this->license->expires));
			$td = $tr->td( $this->license->state);

			if ( $this->invoice->workstation_override) {
				$tr = $tlicense->tr();
				$td = $tr->td( '<em>workstation override applied</em>', ['colspan' => '4']);

			}

		}
		else {
			$tr = $tlicense->tr();
			$td = $tr->td( 'no active license');

		}


		/*-- ---[ foot of invoice ]---
		+-------------------------------------------+
		| text                                      |
		+-------------------------------------------+
		--*/
		$tfoot = new html\table('table borderless');
		$tr = $tfoot->tr();
		$td = $tr->td( sprintf('<div>
			<strong>Banking</strong>

			%s BSB: %s Account: %s

			<p class="lead">
			Please <strong>Quote</strong> Invoice : <strong>%s</strong>
			when making a Bank Deposit</p>

			</div>',
			$this->sys->bank_name,
			$this->sys->bank_bsb,
			$this->sys->bank_account,
			sys::format_invoice_number( $this->invoice->id)));

		$tr = $tfoot->tr();
		$td = $tr->td(
			'<p>Products with a term of <em><strong>year</strong></em> are valid 1 year
			from the payment date. Where the product is an extension, the product will
			be valid 1 year from the expiry date of the previous product</p>');

		if ( 'provisional' == $status) {
			$tr = $tfoot->tr();
			$td = $tr->td( sprintf('<p>Warning : Provisional status will expire on %s</p>', strings::asLocalDate( dao\invoices::ProvisionalExpiry( $this->invoice))));

    }

    if ( !( 'approved' == $this->invoice->state || 'canceled' == $this->invoice->state)) {
      $tr = $tfoot->tr();
      $td = $tr->td( sprintf('<p>Terms : <strong>Payment strictly 14 days</strong></p>', strings::asLocalDate( dao\invoices::ProvisionalExpiry( $this->invoice))));

		}

		$ret = new html\table('table borderless table-invoice');
		$ret->tr()->td( $thead);
		$ret->tr()->td( $tbody);
		$ret->tr()->td( $tlicense);
		$ret->tr()->td( $tfoot);

		// return ( $ret);

		$html = $ret->render( true);
		// return $html;

		// create instance
		$cssToInlineStyles = new TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
		$parts = [
			__DIR__,
			'public',
			'css',
			'minimum.css'
		];
		$css = file_get_contents( implode( DIRECTORY_SEPARATOR, $parts));

		// output
		$html = $cssToInlineStyles->convert( $html, $css);
		return ( $html);


	}

}
