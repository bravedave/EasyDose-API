<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * This work is licensed under a Creative Commons Attribution 4.0 International Public License.
 *      http://creativecommons.org/licenses/by/4.0/
 *
*/  ?>
<style>
i[title="download as CSV"] {margin-top: -18px;}
</style>
<div class="row">
	<div class="col-md-8">
		<input type="search" placeholder="search..." class="form-control" autofocus
			name="<?php print $sid = uniqid( 'ed') ?>"
			id="<?php print $sid ?>"
			/>

	</div>

	<div class="col-md-4 d-none d-md-block">
	<div class="form-check">
	<input type="checkbox" class="form-check-input" checked
	name="<?php print $chkid = uniqid( 'ed') ?>"
	id="<?php print $chkid ?>"
	/>
	<label class="form-check-label" for="<?php print $chkid ?>">
	hide development databases

	</label>

	</div>

	</div>

</div>

<div class="row">
	<div class="col p-0">
		<table class="table table-sm" guid-list>
			<thead>
				<tr>
					<td class="d-none d-lg-table-cell" style="width: 40px;" role="sort-header" data-key="id" data-sorttype="numeric">id</td>
					<td role="sort-header" data-key="guid">guid</td>
					<td class="d-none d-lg-table-cell" style="width: 18em;" role="sort-header" data-key="name">proprietor</td>
					<td class="text-center" style="width: 5em;" role="sort-header" data-key="override">has override</td>
					<td class="text-center" style="width: 8em;" role="sort-header" data-key="created">created</td>
					<td class="d-none d-lg-table-cell text-center" style="width: 8em;" role="sort-header" data-key="updated">updated</td>
					<td class="d-none d-lg-table-cell text-center" style="width: 8em;" role="sort-header" data-key="expires">expires</td>

				</tr>

			</thead>

			<tbody>
			<?php while ( $dto = $this->data->res->dto()) {
				$expires = '';
				if ( $t = strtotime( $dto->expires)) {
					$expires = date( \config::$DATE_FORMAT, strtotime( $dto->expires));

				}  ?>
				<tr
					data-id="<?php print $dto->id ?>"
					data-guid="<?php print $dto->guid ?>"
					data-name="<?php print $dto->name ?>"
					data-created="<?php print $dto->created ?>"
					data-updated="<?php print $dto->updated ?>"
					data-expires="<?php print $dto->expires ?>"
					data-override="<?php
						if ( strtotime( $dto->grace_expires) > time()) {
							print date( 'Y-m-d', strtotime( $dto->grace_expires));
						}
						else {
							print '0000-00-00';

						} ?>"
					data-dev="<?php print (int)$dto->development ?>"
					class="<?php if ( (int)$dto->development) print 'd-none'; ?>"
					row-guid>
					<td class="d-none d-lg-table-cell"><?php print $dto->id ?></td>
					<td><?php print $dto->guid ?>
						<div class="text-muted small">
						<?php printf('%s', $dto->site); ?>

						</div>

					</td>
					<td class="d-none d-lg-table-cell"><?php print $dto->name ?></td>
					<td class="text-center"><?php
						if ( strtotime( $dto->grace_expires) > time() ) {
							print strings::html_tick;
						} ?></td>
					<td class="text-center"><?php print date( \config::$DATE_FORMAT, strtotime( $dto->created)) ?></td>
					<td class="d-none d-lg-table-cell text-center"><?php print date( \config::$DATE_FORMAT, strtotime( $dto->updated)) ?></td>
					<td class="d-none d-lg-table-cell text-center"><?php print $expires; ?></td>

				</tr>

			<?php } ?>

			</tbody>

		</table>

	</div>

</div>
<?php
  // sys::dump( $this->data->res, NULL, FALSE); ?>

<script>
$(document).ready( function() {
  let filter = function(e) {
    let dev = $('#<?php print $chkid ?>').prop('checked');
    let _me = $('#<?php print $sid ?>');
    let t = _me.val();

    $('tr[row-guid]').each( function( i, tr) {
      var _tr = $(tr);

      if ( dev && _tr.data('dev') == '1') {
        _tr.addClass('d-none');

      }
      else {
        if ( t == '') {
          _tr.removeClass('d-none');

        }
        else {
          var rex = new RegExp(t,'i')
          // console.log( t, _tr.text())
          if ( rex.test( _tr.text())) {
            _tr.removeClass('d-none');
          }
          else {
            _tr.addClass('d-none');

          }

        }

      }

    })

  }

  $('#<?php print $sid ?>').on( 'keyup', filter)
  $('#<?php print $chkid ?>').on( 'change', filter);

  $('tr[row-guid]').each( function( i, tr) {
    var _tr = $(tr);
    var id = _tr.data('id');

    _tr
    .addClass('pointer')
    .on( 'click', function( e) {
      window.location.href = _brayworth_.url('guid/view/'+id);

    })
    .on( 'contextmenu', function( e) {
      if (e.shiftKey)
        return;

      e.stopPropagation(); e.preventDefault();

      _brayworth_.hideContexts();
      let _context = _brayworth_.context();
      _context.append( $('<a><i class="fa fa-link"></i>view</a>').attr('href',_brayworth_.url('guid/view/'+id)));

<?php if ( \currentUser::isProgrammer()) { ?>
      _context.append('<hr />');
      _context.append($('<a href="#"><i class="fa fa-exchange"></i>switch to user</a>').on('click', function(e) {
        e.stopPropagation(); e.preventDefault();

        // console.log({
        //   action : 'switch',
        //   guid : _tr.data('guid')
        //
        // });

        _brayworth_.post({
          url : _brayworth_.url('users'),
          data : {
            action : 'switch',
            guid : _tr.data('guid')

          }

        }).then ( function( d) {
          if ( 'ack' == d.response) {
            window.location.href = _brayworth_.url();

          }
          else {
            _brayworth_.growl(d);

          }

        });

        _context.close();

      }));

      _context.append($('<a href="#"><i class="fa fa-trash"></i>delete</a>').on('click', function(e) {
        e.stopPropagation(); e.preventDefault();

        _brayworth_.modal({
          title: 'confirm',
          text: 'Are you sure ?',
          buttons : {
            yes : function( e) {
              hourglass.on();
              window.location.href = _brayworth_.url( 'guid/remove/' + id);

            }

          }

        });

        _context.close();

      }));

<?php } // if ( \currentUser::isProgrammer()) ?>

      _context.open( e);

    })

  });

  /*--[ a CSV download icon ]--*/
  let guidTable = $('table[guid-list]');
  if ( guidTable.length == 1) {
    $('<i class="fa fa-fw fa-table noprint pointer pull-right" title="download as CSV"></i>')
    .on( 'click', function( e) {
      _ed_.csv.call( guidTable, 'guid-list.csv');
    })
    .insertBefore( guidTable);

  }

});
</script>
