<div class="row">
  <!-- <?php echo form_open_multipart($action_impor) ?> -->
  <div class="col-sm-12">
    <div class="form-group"><label>Upload File Impor Jumlah Diterima (*)</label>
      <input type="file" name="impor_diterima" id="impor_diterima" class="form-control" accept=".xlsx,.xls">
    </div>

    <div class="form-group">
      <button type="submit" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
      <a href="<?php echo $format_diterima ?>" class="btn btn-info"><i class="fa fa-file-excel-o"></i> <?php echo $btn_import ?></a>
    </div>
  </div>
  <!-- <?php echo form_close(); ?>   -->
</div>