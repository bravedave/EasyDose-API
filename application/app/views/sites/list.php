<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/ ?>
  <style>
  i[title="download as CSV"] {
    margin-top: -18px;
  }
  </style>
  <div class="row">
    <div class="col-md-6">
      <input type="search" name="<?= $sid = uniqid( 'ed') ?>"
      id="<?= $sid ?>" placeholder="search..." class="form-control"
      autofocus />

    </div>

  </div>

  <div class="row">
    <div class="col">
      <div class="table-responsive">
        <table class="table table-striped table-sm small" sites-list>
          <thead>
            <tr>
              <td role="sort-header" data-key="state">State</td>
              <td role="sort-header" data-key="site">Site</td>
              <td class="d-none d-xl-table-cell">Tel.</td>
              <td class="d-none d-lg-table-cell text-center"><i class="fa fa-user"></i></td>
              <td class="d-none d-lg-table-cell text-center">@</td>
              <td class="d-none d-lg-table-cell text-center">ABN</td>
              <td class="d-none d-xl-table-cell">IP</td>
              <td role="sort-header" data-key="product" title="License">Lic.</td>
              <td class="d-none d-md-table-cell">Active/<br />Patients</td>
              <td class="d-none d-xl-table-cell">OS</td>
              <td class="d-none d-xl-table-cell" role="sort-header" data-key="workstation">Workstation</td>
              <td class="d-none d-xl-table-cell">Deploy</td>
              <td class="d-none d-lg-table-cell" role="sort-header" data-key="version">Version</td>
              <td class="text-center">Act</td>
              <td role="sort-header" data-key="expires">Expires</td>
              <td class="d-none d-lg-table-cell" role="sort-header" data-key="updated">Update</td>

            </tr>

          </thead>

          <tbody>
            <?php
            $isites = 0;
            $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            while ( $dto = $this->data->sites->dto()) {
              $isites++;
              $tel = '';
              $number = $phoneUtil->parse( $dto->tel, config::country_code);
              if ( $phoneUtil->isValidNumber( $number)) {
                $tel = $phoneUtil->format( $number, \libphonenumber\PhoneNumberFormat::NATIONAL);
              }
              ?>
              <tr data-id="<?= $dto->id ?>"
                data-state="<?= $dto->state ?>"
                data-site="<?= $dto->site ?>"
                data-product="<?= strings::ShortLicense( $dto->productid) ?>"
                data-workstation="<?= $dto->workstation ?>"
                data-version="<?= $dto->version ?>"
                data-expires="<?= $dto->expires ?>"
                data-updated="<?= $dto->updated ?>"
                site>

                <td><?= $dto->state ?></td>
                <td class="text-nowrap">
                  <?= $dto->site ?>
                </td>
                <td class="d-none d-xl-table-cell text-nowrap"><?= $tel ?></td>
                <td class="d-none text-center d-lg-table-cell"><i class="fa fa-fw <?= ( $dto->guid_user_id ? 'fa-check text-info' : 'fa-times text-danger') ?>"></i></td>
                <td class="d-none text-center d-lg-table-cell"><i class="fa fa-fw <?= ( $dto->email ? 'fa-check text-info' : 'fa-times text-danger') ?>"></i></td>
                <td class="d-none text-center d-lg-table-cell"><i class="fa fa-fw <?= ( $dto->abn ? 'fa-check text-info' : 'fa-times text-danger') ?>"></i></td>
                <td class="d-none d-xl-table-cell"><?= $dto->ip ?></td>
                <td><?= strings::ShortLicense( $dto->productid); ?></td>
                <td class="d-none d-md-table-cell"><?= sprintf( '%s/%s', $dto->patientsActive, $dto->patients) ?></td>
                <td class="d-none d-xl-table-cell"><?= strings::StringToOS($dto->os) ?></td>
                <td class="d-none d-xl-table-cell"><?= $dto->workstation ?></td>
                <td class="d-none d-xl-table-cell"><?= $dto->deployment ?></td>
                <td class="d-none d-lg-table-cell"><?= preg_replace( '@^V2\.2\.@', '', $dto->version) ?></td>
                <td class="text-center"><i class="fa fa-fw <?= ( $dto->activated ? 'fa-circle text-info' : 'fa-times text-danger') ?>"></i></td>
                <td><?= date( \config::$DATE_FORMAT, strtotime( $dto->expires )) ?></td>
                <td class="d-none d-lg-table-cell text-center"><?= strings::asShortDateTime( $dto->updated) ?></td>

              </tr>

              <?php
            } // while ( $dto = $this->data->sites->dto()) ?>

          </tbody>

        </table>

      </div>

    </div>

  </div>

  <div class="row">
    <div class="col">
      <em><?php printf( 'count: %s', $isites); ?></em>

    </div>

    <div class="col text-right">
      <em><?= date( 'c'); ?></em>

    </div>

  </div>

  <script>
  $(document).ready( function() {
    $('#<?= $sid ?>').on( 'keyup', function(e) {
      var _me = $(this);
      var t = _me.val();

      $('tr[site]').each( function( i, tr) {
        var _tr = $(tr);

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

      });

    });

    $('tr[site]').each( function( i, tr) {
      let _tr = $(tr);

      _tr.addClass('pointer').on( 'click', function( e) {
        if ( e.shiftKey) {
          return;

        }

        window.location.href = _brayworth_.url('sites/view/' + _tr.data('id'));

      })
      .on( 'delete', function(e) {
        let _tr = $(this);
        let _data = _tr.data();

        hourglass.on();

        _brayworth_.post({
          url : _brayworth_.url('sites'),
          data : {
            action : 'delete',
            id : _tr.data('id'),

          }

        })
        .then( function(d) {
          _brayworth_.growl(d);
          if ( 'ack' == d.response) {
            _tr.remove();

          }

          hourglass.off()

        });

      })
      .on( 'contextmenu', function(e) {
        if ( e.shiftKey) {
          return;

        }

        e.stopPropagation(); e.preventDefault();

        let _tr = $(this);
        let _data = _tr.data();
        _brayworth_.hideContexts();

        let context = _brayworth_.context();
        let updated = _brayworth_.moment( _data.updated);
        let duration = moment.duration( _brayworth_.moment().diff( updated));

        if ( duration.asDays() > 3 || 'EasyDose Unkown Business' == _data.site || 'MOON' == _data.workstation) {
          context.append( $('<a href="#"><i class="fa fa-trash"></i>delete</a>').on( 'click', function(e) {
            e.stopPropagation(); e.preventDefault();

            context.close();

            _brayworth_.modal({
              title : 'confirm',
              text : 'Are you Sure ?',
              width : 300,
              buttons : {
                'Yes - Delete' : function() {
                  this.close();
                  _tr.trigger( 'delete');

                }

              }

            });

          }));

          context.open( e);

        }

      });

    });

    /*--[ a CSV download icon ]--*/
    var sitesTable = $('table[sites-list]');
    if ( sitesTable.length == 1) {
      $('<i class="fa fa-fw fa-table noprint pointer pull-right" title="download as CSV"></i>')
      .on( 'click', function( e) {
        _ed_.csv.call( sitesTable, 'sites-list.csv');
      })
      .insertBefore( sitesTable);

    }

  });
  </script>
