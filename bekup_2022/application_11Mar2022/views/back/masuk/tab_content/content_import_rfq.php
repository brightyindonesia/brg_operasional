<div class="form-group">
  <a href="<?php echo $import_action_rfq ?>" class="btn btn-success"><i class="fa fa-file-excel-o"></i> <?php echo $btn_import ?></a>
</div>
<?php // echo form_open_multipart($action_impor_rfq) ?>
  
    <div class="form-group"><label>Upload File Impor Request For Quotation (*)</label>
      <input type="file" name="impor_rfq" id="impor_rfq" class="form-control" accept=".xlsx,.xls">
    </div>
  <div class="form-group">
    <button type="submit" name="button"  id="import-rfq" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
    <button type="reset" name="button" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $btn_reset ?></button>
  </div>
  <!-- /.box-body -->
<?php // echo form_close() ?>