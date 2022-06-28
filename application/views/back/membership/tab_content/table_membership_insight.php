<div class="box box-primary no-border">
<div class="box-header">
  <?php echo form_dropdown('tier', $get_all_tier, '', $tier) ?>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <div class="table-responsive">
      <table id="table-listing-membership" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th style="text-align: center">#</th>
            <th style="text-align: center">Nama Customer</th>
            <th width="27%" style="text-align: center">No. Hp</th>
            <th style="text-align: center">Total Belanja</th>
            <th style="text-align: center">Poin</th>

          </tr>
        </thead>
      </table>
    </div>
  </div>
  <!-- /.box-body -->
  <div class="box-footer">
  </div>
</div>
<!-- /.box -->