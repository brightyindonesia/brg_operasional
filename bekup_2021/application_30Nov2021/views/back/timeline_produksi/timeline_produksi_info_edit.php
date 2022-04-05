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
        <?php 
          echo form_open($action);
        ?>
          <div class="box-body">
            <div class="row">
              <div class="col-sm-6">
                  <div class="form-group"><label>Nomor Produksi (*)</label>
                    <?php echo form_input($nomor_request, str_replace("PO","TML",$timeline->no_po)) ?>
                  </div>

                  <div class="form-group"><label>Nama SKU (*)</label>
                    <?php echo form_dropdown('sku', $get_all_sku, $timeline->id_sku, $sku) ?>
                  </div>

                  <div class="form-group"><label>Nama Vendor (*)</label>
                    <?php echo form_input($nama_vendor, $timeline->nama_vendor) ?>
                  </div>
              </div>

              <div class="col-sm-6">
                  <div class="form-group"><label>Nomor Purchase Order (*)</label>
                    <?php echo form_input($nomor_request, $timeline->no_po) ?>
                  </div>

                  <div class="form-group"><label>Nama Kategori (*)</label>
                    <?php echo form_dropdown('kategori', $get_all_kategori, $timeline->id_kategori_po, $kategori) ?>
                  </div>

                  <div class="form-group"><label>Jumlah Produksi (*)</label>
                    <?php echo form_input($qty, $timeline->total_kuantitas_po) ?>
                  </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="form-group"><label>Start - End Date (*)</label>
                  <input type="text" name="periodik" class="form-control float-right" value="<?php echo $detail_timeline->start_date_detail_timeline_produksi.' - '.$detail_timeline->end_date_detail_timeline_produksi ?>" id="range-date">
                </div>
              </div>
              
              <div class="col-sm-6">
                <div class="form-group"><label>Keterangan</label>
                  <?php echo form_textarea($keterangan, $detail_timeline->ket_detail_timeline_produksi) ?>
                </div>
              </div>
            </div>

            <?php echo form_input($id, str_replace("PO","TML",$timeline->no_po)) ?>
            <?php echo form_input($id_po, $timeline->no_po) ?>
            <?php echo form_input($id_detail, $detail_timeline->id_detail_timeline_produksi) ?>
            <?php echo form_input($id_sku, $timeline->id_sku) ?>
            <?php echo form_input($qty_produksi, $timeline->total_kuantitas_po) ?>
          </div>
          <div class="box-footer">
            <button type="submit" id="bahan-info" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
            <button type="reset" name="button" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $btn_reset ?></button>
          </div>
      </div>
      <?php 
        echo form_close();
      ?>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>
  <!-- date-range-picker -->
  <script src="<?php echo base_url('assets/plugins/') ?>moment/min/moment.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>bootstrap-daterangepicker/daterangepicker.js"></script>

  <script type="text/javascript">
    $('#range-date').daterangepicker({
        locale: {
          format: 'YYYY/MM/DD'
        }
    });
    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

    $('#example3').DataTable({
      "paging": false,
      "lengthChange": false,
      "searching": false,
      "ordering": false,
      "info": false,
      "autoWidth": false
    });

    // submit form masuk
    $('#bahan-info').click(function(e){
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

      var id_detail = document.getElementById('id-detail').value;
      var nomor_po = document.getElementById('nomor-po').value;
      var nomor_produksi = document.getElementById('nomor-produksi').value;
      var keterangan = document.getElementById('keterangan').value;
      var qty = document.getElementById('qty-produksi').value;
      var sku = document.getElementById('sku').value;
      var date = document.getElementById('range-date').value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
      csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      // alert(panjangArray);
      if (nomor_po == '' && nomor_produksi == '' && keterangan == '' && sku == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Harap diisi!'
        });
      }else{
        $.ajax({ 
          url:"<?php echo base_url()?>admin/timeline_produksi/info_proses_edit",
          method:"post",
          dataType: 'JSON', 
          data:{id_detail: id_detail, nomor_po:nomor_po, nomor_produksi: nomor_produksi, keterangan: keterangan, qty: qty, sku: sku, date: date, [csrfName]: csrfHash},
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
                window.location.replace("<?php echo base_url()?>admin/timeline_produksi/history/"+data.produksi);
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
