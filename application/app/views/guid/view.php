<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/ ?>
<div class="row pb-1">
  <div class="col col-2 small">
    id
  </div>

  <div class="col col-10">
    <td><?php print $this->data->dto->id ?></td>

  </div>

</div>

<div class="row pb-1">
  <div class="col col-2 small">
    guid
  </div>

  <div class="col col-10">
    <?php print $this->data->dto->guid ?>

  </div>

</div>

<div class="row pb-1">
  <div class="col col-2 small">
    account
  </div>

  <div class="col col-10">
    <?php
    if ( $this->data->account) {
      printf('%s (%s)', $this->data->account->name, $this->data->account->id);

    }
    else {
      printf( '<button class="btn btn-sm btn-outline-primary" id="assign-account">assign account</button>');

    }

    ?>
  </div>

</div>

<div class="row pb-1">
  <div class="col col-2 small">
    created
  </div>

  <div class="col col-10">
    <?php print date( \config::$DATE_FORMAT, strtotime( $this->data->dto->created)) ?>

  </div>

</div>

<div class="row pb-1">
  <div class="col col-2 small">
    updated
  </div>

  <div class="col col-10">
    <?php print date( \config::$DATE_FORMAT, strtotime( $this->data->dto->updated)) ?>

  </div>

</div>

<div class="row pb-1">
  <div class="col col-2 small">
    sites
  </div>

  <div class="col col-10">
    <table class="table table-striped">
      <colgroup>
        <col />
        <col />
        <col />
      </colgroup>
      <tbody>
<?php while ($dto = $this->data->sites->dto()) {  ?>
        <tr data-id="<?php print $dto->id ?>" site>
          <td><?php print $dto->site ?></td>
          <td><?php print $dto->state ?></td>
          <td><?php print $dto->patientsActive ?>/<?php print $dto->patients ?></td>

        </tr>

<?php } // while ($dto = $this->data->sites->dto())  ?>

      </tbody>

    </table>

  </div>

</div>

<?php
  if ( $this->data->license) {  ?>

<div class="row pb-1">
  <div class="col col-2 small">
    license
  </div>

  <div class="col col-10">
    <table class="table table-striped table-sm">
      <colgroup>
          <col style="width: 5em" />
          <col />
          <col style="width: 4em" />
          <col style="width: 12em" />

      </colgroup>

      <thead>
        <tr>
          <td>type</td>
          <td>product</td>
          <td>wks</td>
          <td>expires</td>

        </tr>

      </thead>

      <tbody>
        <tr>
          <td><?php print $this->data->license->type; ?></td>
          <td><?php print $this->data->license->product; ?></td>
          <td><?php print $this->data->license->workstations; ?></td>
          <td><?php print strings::asShortDate( $this->data->license->expires); ?></td>

        </tr>

      </tbody>

    </table>

<?php
    sys::dump( $this->data->dto, NULL, FALSE);
    ?>

  </div>

</div>

<?php
  } ?>

<div class="row pb-1">
  <div class="col col-2 small">
    license override
  </div>

  <div class="col col-10">
    <form class="form" method="post" action="<?php url::write('guid') ?>">
      <table class="table table-striped table-sm">
        <colgroup>
          <col style="width: 5em" />
          <col />
          <col style="width: 4em" />
          <col style="width: 12em" />

        </colgroup>

        <thead>
          <tr>
            <td>&nbsp;</td>
            <td>product</td>
            <td>wks</td>
            <td>expires</td>

          </tr>

        </thead>

        <tbody>
          <tr>
            <td>&nbsp;</td>
            <td>
              <select class="form-control" name="grace_product">
                <option></option>
                <option value="easydose5" <?php if ( 'easydose5' == $this->data->dto->grace_product) print "selected"; ?>>easydose5</option>
                <option value="easydose10" <?php if ( 'easydose10' == $this->data->dto->grace_product) print "selected"; ?>>easydose10</option>
                <option value="easydoseOPEN" <?php if ( 'easydoseOPEN' == $this->data->dto->grace_product) print "selected"; ?>>easydoseOPEN</option>

              </select>

            </td>
            <td>
              <input type="number" name="grace_workstations" class="form-control" value="<?php print $this->data->dto->grace_workstations ?>" />

            </td>
            <td>
              <input type="date" name="grace_expires" class="form-control" value="<?php print $this->data->dto->grace_expires ?>" />

            </td>

          </tr>

          <tr>
            <td>&nbsp;</td>
            <td colspan="3">
              <input type="submit" class='btn btn-primary' value="apply license override" />

            </td>

          </tr>

        </tbody>

      </table>

    </form>

  </div>

</div>

<script>
$(document).ready( function() {
  $('tr[site]').each( function( i, tr) {
    var _tr = $(tr);
    var id = _tr.data( 'id');

    _tr
    .addClass('pointer')
    .on( 'click', function( e) {
      window.location.href = _brayworth_.url('sites/view/'+id);

    })
    .on( 'contextmenu', function( e) {
      if (e.shiftKey)
        return;

      e.stopPropagation(); e.preventDefault();

      var _context = _brayworth_.context();
      _context.append($('<a><i class="fa fa-link"></i>view</a>').attr('href',_brayworth_.url('sites/view/'+id)))
      _context.append($('<a href="#"><i class="fa fa-trash"></i>delete</a>').on('click', function(e) {
        e.stopPropagation(); e.preventDefault();

        _brayworth_.modal({
          title: 'confirm',
          text: 'Are you sure ?',
          buttons : {
            yes : function( e) {
              hourglass.on();
              window.location.href = _brayworth_.url( 'sites/remove/' + id + '/<?php print $this->data->dto->id ?>');

            }

          }

        });

        _context.close();

      }));

      _context.open( e);

    })

  });

  $('#assign-account').on('click', function(e) {
    e.stopPropagation();

    var fld = $('<input type="text" class="form-control" autocomplete="email" placeholder="@" />');

		var d = $('<div />');
		d.append( fld);

		var modal = _brayworth_.modal({
			title : 'email address',
			text : d,
			width : 400,
			onOpen : function() {
				fld.on('keypress', function( e) {
          if ( e.keyCode == 13) {
            var _fld = $(this);
            var email = _fld.val();
            if ( email.isEmail()) {

              modal.modal('close');

              _brayworth_.post({
                url : _brayworth_.url('api'),
                data : {
                  action : 'set-account',
                  email : email,
                  guid : '<?php print $this->data->dto->guid ?>',
                }

              })
              .then( function(d) {
                _brayworth_.growl(d);
                if ( 'ack' == d.response)
                  setTimeout( function() { window.location.reload()}, 500);

              })

            }

          }

				});

			},

		});

  })

})
</script>
