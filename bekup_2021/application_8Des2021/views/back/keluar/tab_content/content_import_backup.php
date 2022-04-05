<div class="form-group">
  <a href="<?php echo $backup_db_action ?>" class="btn btn-primary"><i class="fa fa-database"></i> <?php echo $btn_backup ?></a>
</div>
<?php echo form_open_multipart($action_restore) ?>  
<div class="form-group"><label>Upload File Database (*)</label>
  <input type="file" name="restore_db" id="restore_db" class="form-control" accept=".sql">
</div>
<div class="form-group">
  <button type="submit" name="button" class="btn btn-primary"><i class="fa fa-database"></i> <?php echo $btn_restore ?></button>
  <button type="reset" name="button" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $btn_reset ?></button>
</div>
<!-- /.box-body -->
<?php echo form_close() ?>