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

                  <div class="form-group"><label>Jumlah Produksi / Terkirim (*)</label>
                    <?php echo form_input($qty, $timeline->total_produksi." / ".$timeline->total_produksi_jadi) ?>
                  </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="form-group"><label>Start - End Date (*)</label>
                  <input type="text" class="form-control" disabled value="<?php echo $detail_timeline->start_date_detail_timeline_produksi.' - '.$detail_timeline->end_date_detail_timeline_produksi ?>">
                </div>
              </div>
              
              <div class="col-sm-6">
                <div class="form-group"><label>Keterangan</label>
                  <?php echo form_textarea($keterangan, $detail_timeline->ket_detail_timeline_produksi) ?>
                </div>
              </div>
            </div>

            <br>

            <div class="row">
              <div id="hasil_val" style="display: none;" class="col-sm-12">
              </div>              
            </div>

            <div class="row">
                <div class="col-sm-12">
                  <div class="table-responsive">
                    <label>Daftar Bahan Produksi </label>
                      <table id="example3" class="table table-bordered table-striped table-responsive">
                      <thead>
                        <tr align="center">
                          <th width="15%">Kode SKU</th>
                          <th width="75%">Nama Bahan</th>
                          <th>Qty</th>
                        </tr>
                      </thead>

                      <tbody>
                        <tr>
                          <td>
                            <?php echo $detail_timeline->kode_sku_bahan_kemas; ?>
                          </td>
                          <td>
                            <?php echo $detail_timeline->nama_bahan_kemas; ?>
                          </td>
                          <td>
                            <?php echo $detail_timeline->qty_detail_timeline_produksi; ?>
                          </td>
                        </tr>
                        <?php
                        ?>
                      </tbody>

                      <tfoot>
                        <tr align="center">
                          <th>Kode SKU</th>
                          <th>Nama Bahan</th>
                          <th>Qty</th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>

              <br>

              <div class="row">
                <div class="col-sm-12">
                  <div class="table-responsive">
                    <label>Daftar Bahan Produksi Reject</label>
                      <table id="example3" class="table table-bordered table-striped ">
                      <thead>
                        <tr align="center">
                          <th>Nomor PO</th>
                          <th width="75%">Nama Bahan</th>
                          <th>Qty</th>
                          <th>Jumlah Terpakai</th>
                          <th>Jumlah Sisa</th>
                          <th>Jumlah Reject</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php 
                          $i = 0;
                          foreach ($bahan as $row_r) {
                            if ($row_r->qty_detail_timeline_produksi != 0) {
                        ?>
                        <tr>
                          <td>
                            <input type="hidden" id="dr_po[]" name="dr_po[]" value="<?php echo $row_r->no_po; ?>">
                            <input type="hidden" id="dr_id[]" name="dr_id[]" value="<?php echo $row_r->id_bahan_kemas; ?>">
                            <input type="hidden" id="dr_id_posi[]" name="dr_id_posi[]" value="<?php echo $row_r->id_posi_data_access; ?>">
                            <input type="hidden" id="dr_id_detail[]" name="dr_id_detail[]" value="<?php echo $row_r->id_detail_timeline_produksi; ?>">
                            <?php echo $row_r->no_po; ?>
                          </td>
                          <td>
                            <?php echo $row_r->nama_bahan_kemas; ?>
                          </td>
                          <td>
                              <input type="hidden" id="dr_qty<?php echo $i ?>" name="dr_qty[]" value="<?php echo $row_r->qty_detail_timeline_produksi ?>">
                              <?php echo $row_r->qty_detail_timeline_produksi ?>
                          </td>
                          <td>
                            <input style="width: 110px;text-align: center;" type="text" oninput="checkValue(this);calculate('dr_qty<?php echo $i ?>', 'dr_terpakai<?php echo $i ?>', 'dr_sisa<?php echo $i ?>', 'dr_sisa_terpakai<?php echo $i ?>', 'dr_selisih<?php echo $i ?>');" min="0" max="<?php echo $row_r->qty_detail_timeline_produksi ?>" name="dr_terpakai[]" id="dr_terpakai<?php echo $i ?>" value="<?php echo $row_r->terpakai_detail_timeline_produksi ?>">
                          </td>
                          <td>
                            <input style="width: 110px;text-align: center;" type="hidden" name="dr_sisa[]" id="dr_sisa<?php echo $i ?>" value="<?php echo $row_r->sisa_detail_timeline_produksi ?>">
                            <input disabled style="width: 110px;text-align: center;" type="text" id="dr_sisa_terpakai<?php echo $i ?>" value="<?php echo $row_r->sisa_detail_timeline_produksi ?>">
                          </td>

                          <td>
                            <input style="width: 110px;text-align: center;" type="text" oninput="checkValue(this);" name="dr_selisih[]" id="dr_selisih<?php echo $i ?>" value="<?php echo $row_r->selisih_detail_timeline_produksi ?>" min="0" max="<?php echo $row_r->qty_detail_timeline_produksi ?>">
                          </td>
                        </tr>
                        <?php
                            }
                            $i++;
                          }
                        ?>
                      </tbody>

                      <tfoot>
                        <tr align="center">
                          <th>Nomor PO</th>
                          <th>Nama Bahan</th>
                          <th>Qty</th>
                          <th>Jumlah Terpakai</th>
                          <th>Jumlah Sisa</th>
                          <th>Jumlah Reject</th>
                        </tr>
                      </tfoot>
                    </table>
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
            <button type="submit" id="bahan-delivery-add-posi" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
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

    $(document).on('click', '#hps_row', function(){
        $(this).closest('tr').remove();
    });

    function calculate(qtyId,terpakaiId,sisaId, sisaterpakaiId, selisihId)
    {
       quantity = document.getElementById(qtyId).value;
       selisih = document.getElementById(terpakaiId).value;

       total = quantity - selisih;
       if(isNaN(total))
       {
        total = 0;
       }

       document.getElementById(sisaId).value = total;
       document.getElementById(sisaterpakaiId).value = total;
       document.getElementById(selisihId).value = total;

       $('#'+selisihId).attr({
           "max" : total,        // substitute your own
           "min" : 0          // values (or variables) here
        });
    }

    // function val_qty(id)
    // {
    //   var dt_qty =  $("input[name='dt_qty[]']")
    //         .map(function(){return $(this).val();}).get();
    //   var dt_selisih =  $("input[name='dt_selisih[]']")
    //         .map(function(){return $(this).val();}).get();
    //   var panjangArray = dt_qty.length;
    //   if (dt_qty[id] == '' || dt_qty[id] == 0) {
    //     document.getElementById("hasil_val").style.display = "block";
    //     document.getElementById("hasil_val").innerHTML = "<div class='alert alert-danger'>Tidak boleh kosong!</div>";
    //     $("#bahan-delivery").prop("disabled",true);
    //   }else if (dt_qty[id] > dt_selisih[id]) {
    //     document.getElementById("hasil_val").style.display = "block";
    //     document.getElementById("hasil_val").innerHTML = "<div class='alert alert-danger'>Stok hanya tersedia " + dt_selisih[id] + "</div>";
    //     $("#bahan-delivery").prop("disabled",true);
    //   }else if(dt_qty[id] <= dt_selisih[id]){
    //     document.getElementById("hasil_val").style.display = "none";
    //     document.getElementById("hasil_val").innerHTML = "";
    //     $("#bahan-delivery").prop("disabled",false);
    //   }
    // }

    // this checks the value and updates it on the control, if needed
    function checkValue(sender) {
        let min = sender.min;
        let max = sender.max;
        let value = parseInt(sender.value);
        if (value>max) {
            sender.value = min;
        } else if (value<min) {
            sender.value = max;
        }
    }

    // submit form masuk
    $('#bahan-delivery-add-posi').click(function(e){
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
      // Untuk tabel reject
      var dr_po       =  $("input[name='dr_po[]']")
            .map(function(){return $(this).val();}).get();
      var dr_id       =  $("input[name='dr_id[]']")
            .map(function(){return $(this).val();}).get();
      var dr_id_posi  =  $("input[name='dr_id_posi[]']")
            .map(function(){return $(this).val();}).get();
      var dr_id_detail=  $("input[name='dr_id_detail[]']")
            .map(function(){return $(this).val();}).get();
      var dr_qty =  $("input[name='dr_qty[]']")
            .map(function(){return $(this).val();}).get();
      var dr_terpakai =  $("input[name='dr_terpakai[]']")
            .map(function(){return $(this).val();}).get();
      var dr_sisa =  $("input[name='dr_sisa[]']")
            .map(function(){return $(this).val();}).get();
      var dr_selisih =  $("input[name='dr_selisih[]']")
            .map(function(){return $(this).val();}).get();
      var JS_dr_po = JSON.stringify(dr_po);
      var JS_dr_id = JSON.stringify(dr_id);
      var JS_dr_id_posi = JSON.stringify(dr_id_posi);
      var JS_dr_id_detail = JSON.stringify(dr_id_detail);
      var JS_dr_qty = JSON.stringify(dr_qty);
      var JS_dr_terpakai = JSON.stringify(dr_terpakai);
      var JS_dr_sisa = JSON.stringify(dr_sisa);
      var JS_dr_selisih = JSON.stringify(dr_selisih);
      var panjangArray_dr = dr_id.length;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
      csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      
      if (nomor_po == '' && nomor_produksi == '' && keterangan == '' && sku == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Harap diisi!'
        });
      }else{
        $.ajax({ 
          url:"<?php echo base_url()?>admin/timeline_produksi/proses_edit_posi_delivery",
          method:"post",
          dataType: 'JSON',
          data:{id_detail: id_detail,nomor_po:nomor_po, nomor_produksi: nomor_produksi, keterangan: keterangan, qty: qty, sku: sku, dr_id: JS_dr_id, dr_id_posi: JS_dr_id_posi, dr_id_detail: JS_dr_id_detail, dr_po: JS_dr_po, dr_qty: JS_dr_qty, dr_terpakai: JS_dr_terpakai, dr_sisa: JS_dr_sisa, dr_selisih: JS_dr_selisih, length_dr: panjangArray_dr, [csrfName]: csrfHash},
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
                window.location.replace("<?php echo base_url()?>admin/timeline_produksi/timeline");
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
