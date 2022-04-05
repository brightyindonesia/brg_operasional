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
          // echo form_open($action);
        ?>
          <div class="box-body">
            <div class="row">
              <div class="col-sm-6">
                  <div class="form-group"><label>Nomor Produksi</label>
                    <?php echo form_input($nomor_request, $timeline->no_timeline_produksi) ?>
                  </div>

                  <div class="form-group"><label>Nama SKU (*)</label>
                    <?php echo form_dropdown('sku', $get_all_sku, $timeline->id_sku, $sku) ?>
                  </div>

                  <div class="form-group"><label>Nama Vendor</label>
                    <?php echo form_input($nama_vendor, $timeline->nama_vendor) ?>
                  </div>
              </div>
              <div class="col-sm-6">
                  <div class="form-group"><label>Nomor Purchase Order</label>
                    <?php echo form_input($nomor_request, $timeline->no_po) ?>
                  </div>

                  <div class="form-group"><label>Nama Kategori (*)</label>
                    <?php echo form_dropdown('kategori', $get_all_kategori, $timeline->id_kategori_po, $kategori) ?>
                  </div>

                  <div class="form-group"><label>Jumlah Produksi / Terkirim</label>
                    <?php echo form_input($qty, $timeline->total_produksi." / ".$timeline->total_produksi_jadi) ?>
                  </div>
              </div>
            </div>

            <?php echo form_input($id, $timeline->no_timeline_produksi) ?>
            <?php echo form_input($id_po, $timeline->no_po) ?>
            <?php echo form_input($id_sku, $timeline->id_sku) ?>
            <?php echo form_input($qty_produksi, $timeline->total_kuantitas_po) ?>
          </div>

          <div class="box-body">            
            <div class="form-group">
              <div class="table-responsive">
                <label>Daftar Bahan Produksi </label>
                  <table id="example3" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th style="text-align: center">No</th>
                        <th style="text-align: center">Nomor PO</th>
                        <th style="text-align: center">Bahan Kemas</th>
                        <th style="text-align: center">Harga</th>
                        <th style="text-align: center">
                          <input type="checkbox" id="master" onclick="totalIt()" >
                        </th>
                      </tr>
                    </thead>

                    <tbody id="details">
                      <?php 
                        $i = 1;
                        foreach ($bahan_po as $val_po) {
                      ?>
                        <tr>
                          <td style="text-align: center"><?php echo $i ?></td>
                          <td style="text-align: center" id="po_bahan"><?php echo $val_po->no_po ?></td>
                          <td style="text-align: center"><?php echo $val_po->nama_bahan_kemas ?></td>
                          <input type="hidden" id="po_id_bahan" value="<?php echo $val_po->id_bahan_kemas ?>">
                          <td style="text-align: center" id="po_harga"><?php echo $val_po->harga_po ?></td>
                          <td style="text-align: center">
                            <input name="product" id="checkbox_detail" value="<?php echo $val_po->harga_po ?>" type="checkbox" class="sub_chk" onclick="totalIt()" />
                          </td>
                        </tr>
                      <?php 
                          $i++;
                        }
                      ?>

                      <?php 
                        foreach ($bahan_kemas as $val_bahan) {
                      ?>
                        <tr>
                          <td style="text-align: center"><?php echo $i ?></td>
                          <td style="text-align: center" id="po_bahan"><?php echo $val_bahan->no_po ?></td>
                          <td style="text-align: center"><?php echo $val_bahan->nama_bahan_kemas ?></td>
                          <input type="hidden" id="po_id_bahan" value="<?php echo $val_bahan->id_bahan_kemas ?>">
                          <td style="text-align: center" id="po_harga"><?php echo $val_bahan->harga_po ?></td>
                          <td style="text-align: center">
                            <input name="product" id="checkbox_detail" value="<?php echo $val_bahan->harga_po ?>" type="checkbox" class="sub_chk" onclick="totalIt()" />
                          </td>
                        </tr>
                      <?php 
                          $i++;
                        }
                      ?>
                    </tbody>

                    <tfoot>
                      <tr>
                        <th style="text-align: center">No</th>
                        <th style="text-align: center">Nomor PO</th>
                        <th style="text-align: center">Bahan Kemas</th>
                        <th style="text-align: center">Harga</th>
                        <th style="text-align: center">#</th>
                      </tr>
                    </tfoot>
                  </table>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12">
                 <div class="form-group">
                   <label>
                      Keterangan
                   </label>
                   <textarea id="keterangan" class="form-control" placeholder="Masukan Keterangan"></textarea>
                 </div>
              </div> 
            </div>

            <div class="row">
              <div class="col-sm-6">
                 <div class="form-group">
                   <label>
                      <input type="radio" name="harga" value="rekomendasi" checked>
                      Harga Rekomendasi
                   </label>
                   <input class="form-control" value="0" readonly type="text" id="rekom">
                 </div>
              </div> 

              <div class="col-sm-6">
                 <div class="form-group">
                    <label>
                      <input type="radio" name="harga" value="custom">
                      Harga Custom
                    </label>
                   <input class="form-control" value="0" type="text" id="custom">
                 </div>
              </div> 
            </div>
          </div>

          <div class="box-footer">
            <!-- <button type="submit" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button> -->
            <button type="submit" id="tambah_hpp" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
            <a href="<?php echo base_url('admin/timeline_produksi/timeline') ?>" class="btn btn-primary"><i class="fa fa-table"></i> Kembali ke Data</a>
            <!-- <button type="reset" name="button" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $btn_reset ?></button> -->
          </div>
      </div>
      <?php 
        // echo form_close();
      ?>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables-bs/css/dataTables.bootstrap.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>datatables/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables-bs/js/dataTables.bootstrap.min.js"></script>
  
  <script type="text/javascript">
    function jsonify(){

        var rows = $('#details tr');
        var a = [];
        rows.each(function(){

            if($(this).find('#checkbox_detail').is(':checked'))
            {
               var po_bahan      = $(this).find('#po_bahan').html();
               var po_harga      = $(this).find('#po_harga').html();
               var bahan_id      = $(this).find('#po_id_bahan').val();

               var x = {
                    po_bahan:po_bahan,
                    po_harga:po_harga,
                    bahan_id:bahan_id,
                };
                a.push(x);
            }
        });
        var c = JSON.stringify(a);
        return c;
    }

    $('#tambah_hpp').click(function(){
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
        
       data = jsonify();
       if ($("input[name='harga']:checked").val() == 'rekomendasi') {
        var jenis = 'Harga Rekomendasi. ';
        var total = document.getElementById('rekom').value;
       }else if($("input[name='harga']:checked").val() == 'custom'){
        var jenis = 'Harga Custom. ';
        var total = document.getElementById('custom').value;
       }
       var nomor_produksi = document.getElementById('nomor-produksi').value;
       var keterangan = document.getElementById('keterangan').value;
       var sku = document.getElementById('sku').value;
       var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
       csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
       
       $.ajax({ 
          url:"<?php echo base_url()?>admin/timeline_produksi/proses_cek_bahan_produksi",
          method:"post",
          dataType: 'JSON', 
          data:{details: data, sku: sku, jenis: jenis, nomor_produksi: nomor_produksi, total: total, keterangan: keterangan, [csrfName]: csrfHash},
          success:function(data)  {  
            // alert(data);
            // if (data.validasi) {
            //   Toast.fire({
            //     icon: 'error',
            //     title: 'Perhatian!',
            //     text: data.validasi
            //   })
            // }

            if (data.sukses) {
              Toast.fire({
                icon: 'success',
                title: 'Sukses!',
                text: data.sukses,
              }).then(function(){
                window.location.replace("<?php echo base_url()?>admin/timeline_produksi/tambah_hpp");
              });
            }
            
          },
          error: function(data){
            console.log(data.responseText);

          } 
        });
    });

    $('#master').on('click', function(e) {
     if($(this).is(':checked',true))  
     {
        $(".sub_chk").prop('checked', true);  
        var input = document.getElementsByName("product");
        var total    = 0;
        for (var i = 0; i < input.length; i++) {
          if (input[i].checked) {
            total += parseFloat(input[i].value);
          }
        }

        if (total == 0) {
          document.getElementById("rekom").value = 0;
          document.getElementById("custom").value = 0;
        }else{
          document.getElementById("rekom").value = total;
          document.getElementById("custom").value = total;  
        }    
     } else {  
        $(".sub_chk").prop('checked',false);  
        var input = document.getElementsByName("product");
        var total    = 0;
        for (var i = 0; i < input.length; i++) {
          if (input[i].checked) {
            total += parseFloat(input[i].value);
          }
        }

        if (total == 0) {
          document.getElementById("rekom").value = 0;
          document.getElementById("custom").value = 0;
        }else{
          document.getElementById("rekom").value = total;
          document.getElementById("custom").value = total;  
        }      
     }  
    });

    function totalIt() {
      var input = document.getElementsByName("product");
      var po = document.getElementsByName("product_po");
      var total    = 0;
      var total_po = 0;
      for (var i = 0; i < input.length; i++) {
        if (input[i].checked) {
          // var cek_checked = $('input[name="product"]:checked').length;
          // if (cek_checked == input.length) {
          //   $("#master").prop('checked',true); 
          // }else{
          //   $("#master").prop('checked',false);
          // }

          total += parseFloat(input[i].value);
        }
      }

      if (total == 0) {
        document.getElementById("rekom").value = 0;
        document.getElementById("custom").value = 0;
      }else{
        document.getElementById("rekom").value = total;
        document.getElementById("custom").value = total;  
      }     
    }

    $(document).ready( function () {
      $('#example1').DataTable();
      $('#example3').DataTable();
    });
    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
