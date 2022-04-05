<?php $this->load->view('back/template/meta'); ?>
<div class="wrapper">

  <?php $this->load->view('back/template/navbar'); ?>
  <?php $this->load->view('back/template/sidebar'); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><?php echo $page_title ?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><?php echo $module ?></li>
        <li class="active"><?php echo $page_title ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <?php if($this->session->flashdata('message')){echo $this->session->flashdata('message');} ?>
      <?php echo validation_errors() ?>
      <div class="box box-primary">
        <?php echo form_open($action) ?>
          <div class="box-body">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group"><label>Changelog Date (*)</label>
                  <?php echo form_input($changelog_date) ?>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group"><label>Changelog Type (*)</label>
                  <?php echo form_dropdown('', $changelog_option, '', $changelog_type) ?>
                </div>
              </div>
            </div>
            <div class="form-group"><label>Changelog Name (*)</label>
              <?php echo form_input($changelog_name) ?>
            </div>
            <div class="form-group"><label>Changelog Description (*)</label>
              <?php echo form_textarea($changelog_description) ?>
            </div>
          </div>
          <div class="box-footer">
            <button type="submit" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
            <button type="reset" name="button" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $btn_reset ?></button>
          </div>
          <!-- /.box-body -->
        <?php echo form_close() ?>
      </div>
      <!-- /.box -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
  <script src="<?php echo base_url() ?>assets/plugins/tinymce/js/tinymce/tinymce.min.js"></script>
  <script type="text/javascript">
  $('#changelog_date').datepicker({
    autoclose: true,
    format: 'yyyy/mm/dd',
    zIndexOffset: 9999,
    todayHighlight: true,
  });

  tinymce.init({
    selector: "textarea",

    // ===========================================
    // INCLUDE THE PLUGIN
    // ===========================================

    plugins: [
      "advlist autolink lists link image charmap print preview anchor",
      "searchreplace visualblocks code fullscreen",
      "insertdatetime media table contextmenu paste jbimages"
    ],

    // ===========================================
    // PUT PLUGIN'S BUTTON on the toolbar
    // ===========================================

    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image jbimages",

    // ===========================================
    // SET RELATIVE_URLS to FALSE (This is required for images to display properly)
    // ===========================================

    relative_urls: false,
    remove_script_host : false,
    convert_urls : true,

  });
  </script>

</div>
<!-- ./wrapper -->

</body>
</html>
