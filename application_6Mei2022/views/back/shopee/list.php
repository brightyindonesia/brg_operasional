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
      <div class="row">
        <?php echo form_open($update_action); ?>
        <div class="col-sm-4">
          <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Data API Shopee</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group"><label>Host (*)</label>
                    <?php echo form_input($host, $shopee->host) ?>
                  </div>

                  <div class="form-group"><label>Redirect Auth (*)</label>
                    <?php echo form_input($redirect, $shopee->redirect_auth) ?>
                  </div>

                  <div class="form-group"><label>Partner ID (*)</label>
                    <?php echo form_input($partner_id, $shopee->partner_id) ?>
                  </div>

                  <div class="form-group"><label>Partner Key (*)</label>
                    <?php echo form_input($partner_key, $shopee->partner_key) ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="box-footer">
              <button type="submit" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
              <a href="javascript(0);" class="btn btn-primary" id="authpartner"> <i class="fa fa-lock"></i> Auth Partner</a>
            </div>
          </div>
        </div>
        <?php echo form_close(); ?>

        <div class="col-sm-4">
          <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Data Shop</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group"><label>Shop ID</label>
                    <?php echo form_input($shop, $shopee->shop_id) ?>
                  </div>

                  <div class="form-group"><label>Merchant ID</label>
                    <?php echo form_input($merchant, $shopee->merchant_id) ?>
                  </div>

                  <div class="form-group"><label>Code</label>
                    <?php echo form_input($code, $shopee->code) ?>
                  </div>
                </div>
              </div>
            </div>
            <div id="authorize_box" class="box-footer" style="display: none;">
              <a href="" id="authorize_link" class="btn btn-success"><i class="fa fa-link"></i> Authorize Account</a>
            </div>
          </div>
        </div>

        <div class="col-sm-4">
          <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Data Token</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group"><label>Access Token</label>
                    <?php echo form_input($access_token, $shopee->access_token) ?>
                  </div>

                  <div class="form-group"><label>Refresh Token</label>
                    <?php echo form_input($refresh_token, $shopee->refresh_token) ?>
                  </div>

                  <div class="form-group"><label>Expire In</label>
                    <?php echo form_input($expire_in, gmdate("H:i:s", $shopee->expire_in)) ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="box-footer">
              <a href="<?php echo base_url('admin/shopee/accesstoken') ?>" class="btn btn-success"><i class="fa fa-download"></i> Get Access Token</a>
              <?php 
              if ($shopee->access_token != '' || $shopee->access_token != NULL && $shopee->refresh_token != '' || $shopee->refresh_token != NULL && $shopee->expire_in != '' || $shopee->expire_in != NULL) {
              ?>
                <a href="<?php echo base_url('admin/shopee/openmenu') ?>" class="btn btn-primary"><i class="fa fa-sign-in"></i> Open Menu Shopee API</a>
              <?php
              }
              ?>
            </div>
          </div>
        </div>

      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>

  <script type="text/javascript">
    $('#authpartner').click(function(e){
      const Toast = Swal.mixin({
        toast: false,
        position: 'center',
        showConfirmButton: false,
        // confirmButtonColor: '#86ccca',
        timer: 3000,
        timerProgressBar: false,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })
      e.preventDefault();

      var redirect = document.getElementById('redirect').value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
      csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

      if (redirect == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Redirect Auth masih kosong!'
        });
      }else{
        $.ajax({ 
          url:"<?php echo base_url()?>admin/shopee/auth",
          method:"post",
          dataType: 'JSON', 
          data:{redirect: redirect, [csrfName]: csrfHash},
          success:function(data)  {  
            // alert(data);
            if (data.validasi) {
              Toast.fire({
                icon: 'error',
                title: 'Perhatian!',
                text: data.validasi
              })
            }

            if (data.sukses) {
              Toast.fire({
                icon: 'success',
                title: 'Sukses!',
                text: data.sukses,
              }).then(function(){
                document.getElementById("authorize_box").style.display = "block";
                $("#authorize_link").attr("href",data.href_link);
              });
            }
            
          },
          error: function(data){
            console.log(data.responseText);
            // Toast.fire({
            //   type: 'warning',
            //   title: 'Perhatian!',
            //   text: data.responseText
            // });

          } 
        });
      }
    });
  </script>

</div>
<!-- ./wrapper -->

</body>
</html>
