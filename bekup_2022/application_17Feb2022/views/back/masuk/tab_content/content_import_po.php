<div class="form-group">
  <a href="<?php echo $import_action_po ?>" class="btn btn-success"><i class="fa fa-file-excel-o"></i> <?php echo $btn_import ?></a>
</div>
<?php echo form_open_multipart($action_impor_po) ?>
  
    <div class="form-group"><label>Upload File Impor Purchase Order (*)</label>
      <input type="file" name="impor_po" id="impor_po" class="form-control" accept=".xlsx,.xls">
    </div>
  <div class="form-group">
    <button type="submit" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
    <button type="reset" name="button" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $btn_reset ?></button>
  </div>
  <!-- /.box-body -->
<?php echo form_close() ?>