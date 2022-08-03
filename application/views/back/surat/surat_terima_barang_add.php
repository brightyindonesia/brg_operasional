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
      <?php if ($this->session->flashdata('message')) {
        echo $this->session->flashdata('message');
      } ?>
      <?php echo validation_errors() ?>
      <div class="box box-primary">
        <div class="box-body">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group"><label>Nomor Surat Terima Barang (*)</label>
                <?php echo form_input($nomor_surat_terima_barang) ?>
              </div>

              <div class="form-group"><label>Kode Surat Jalan (*)</label>
                <?php echo form_input($nomor_surat_jalan) ?>
              </div>

              <div class="form-group"><label>Kode PO (*)</label>
                <?php echo form_dropdown('kode_po', $get_po_list, '', $kode_po) ?>
              </div>

              <div class="form-group"><label>Nama Surat Terima Barang (*)</label>
                <?php echo form_input($nama_surat_terima_barang) ?>
              </div>

              <!-- ROW 2 -->
              <div class="form-group"><label>Nama Warehouse (*)</label>
                <?php echo form_dropdown('warehouse', $get_all_warehouse, '', $warehouse) ?>
              </div>

            </div>

            <div class="col-sm-6">
              <div class="form-group"><label>Tanggal Kirim (*)</label>
                <input type="text" name="periodik_kirim" class="form-control float-right" id="in_periodik_kirim">
              </div>

              <div class="form-group"><label>Nama Pengirim (*)</label>
                <?php echo form_input($nama_pengirim) ?>
              </div>
              <div class="form-group"><label>Tanggal Terima (*)</label>
                <input type="text" name="periodik_terima" class="form-control float-right" id="in_periodik_terima">
              </div>

              <div class="form-group"><label>Nama Penerima (*)</label>
                <?php echo form_dropdown('nama_penerima', $get_penerima_list, '', $nama_penerima) ?>
              </div>

            </div>
          </div>
        </div>
        <div id="dataInput">
        </div>
        <div class="box-body">
          <div class="row">
            <div id="hasil_val" style="display: none;" class="col-sm-12">
            </div>
          </div>
          <div class="row">
            <!-- ROW 1 -->
            <div class="col-sm-3">
              <div class="form-group">
                <label>Deskripsi Barang</label>
                <?php echo form_dropdown('nama_barang', $get_satuan_produk, '', $nama_barang) ?>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label>Kode Barang</label>
                <input type="text" name="kode_barang" id="in_kode_barang" value="" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label>Qty</label>
                <input type="number" name="qty" id="in_qty" value="" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label>Koli/Karton</label>
                <input type="number" name="koli_karton" id="in_koli_karton" value="" class="form-control">
              </div>
            </div>

            <!-- ROW 2 -->
            <div class="col-sm-2">
              <div class="form-group"><label>Nama PIC QC (*)</label>
                <?php echo form_dropdown('pic_qc', $get_all_warehouse, '', $pic_qc) ?>
              </div>
            </div>


            <div class="col-sm-2">
              <div class="form-group">
                <label>Jumlah</label>
                <!-- <input type="text" name="harga" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" id="in_harga"  oninput="cal();" value="" class="form-control"> -->
                <input type="number" name="jumlah_barang_qc" id="in_jumlah_barang_qc" value="" class="form-control">
              </div>
            </div>

            <div class="col-sm-2">
              <div class="form-group">
                <label>Tgl Selesai</label>
                <input type="text" name="tgl_selesai_qc" id="in_tgl_selesai_qc" value="" class="form-control">
              </div>
            </div>

            <div class="col-sm-5">
              <div class="form-group">
                <label>Keterangan</label>
                <input type="text" name="keterangan_qc" id="in_keterangan_qc" value="" class="form-control">
              </div>
            </div>

            <div class="col-sm-1">
              <div class="form-group">
                <label>Tambah</label>
                <button type="button" id="add_bahan_kemas" class="btn btn-success btn-sm form-control">
                  <i class="fa fa-plus"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label>Daftar Barang </label>
                <table id="example3" class="table table-bordered table-striped">
                  <thead>
                    <tr align="center">
                      <th rowspan="2">PIC QC</th>
                      <th rowspan="2">Nama Produk</th>
                      <th rowspan="2">Kode Barang</th>
                      <th rowspan="2" width="5%">Qty</th>
                      <th rowspan="2">Koli/Karton</th>
                      <th colspan="3" style="text-align:center">QC</th>
                      <th rowspan="2" width="1%">Aksi</th>
                    </tr>
                    <tr>
                      <th>Jumlah</th>
                      <th>Tgl Selesai</th>
                      <th>Keterangan</th>
                    </tr>
                  </thead>

                  <tbody>
                  </tbody>

                  <tfoot>
                    <tr align="center">
                      <th>PIC QC</th>
                      <th>Nama Produk</th>
                      <th>Kode Barang</th>
                      <th>Qty</th>
                      <th>Koli/Karton</th>
                      <th>Jumlah</th>
                      <th>Tgl Selesai</th>
                      <th>Keterangan</th>
                      <th>Aksi</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>

        </div>
        <div class="box-footer">
          <button type="submit" id="request-tambah" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
          <button type="reset" name="button" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $btn_reset ?></button>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>.
  <!-- date-range-picker -->
  <script src="<?php echo base_url('assets/plugins/') ?>moment/min/moment.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>bootstrap-daterangepicker/daterangepicker.js"></script>
  <!-- bootstrap datepicker -->
  <script src="<?php echo base_url('assets/plugins/') ?>bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

  <script type="text/javascript">
    // submit form masuk
    $('#request-tambah').click(function(e) {
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

      var nomor_surat_terima = document.getElementById('in_nomor_surat_terima_barang').value;
      var nomor_surat_jalan = document.getElementById('in_nomor_surat_jalan').value;
      var kode_po = document.getElementById('in_kode_po').value;
      var warehouse = document.getElementById('in_warehouse').value;
      var nama_surat = document.getElementById('in_nama_surat_terima_barang').value;
      var periodik_kirim = document.getElementById('in_periodik_kirim').value;
      var nama_pengirim = document.getElementById('in_nama_pengirim').value;
      var periodik_terima = document.getElementById('in_periodik_terima').value;
      var nama_penerima = document.getElementById('in_nama_penerima').value;
      // var remarks = document.getElementById('remarks').value;
      var dt_pic_qc = $("input[name='dt_pic_qc[]']")
        .map(function() {
          return $(this).val();
        }).get();
      var dt_nama_barang = $("input[name='dt_nama_barang[]']")
        .map(function() {
          return $(this).val();
        }).get();
      var dt_kode_barang = $("input[name='dt_kode_barang[]']")
        .map(function() {
          return $(this).val();
        }).get();
      var dt_qty = $("input[name='dt_qty[]']")
        .map(function() {
          return $(this).val();
        }).get();
      var dt_koli_karton = $("input[name='dt_koli_karton[]']")
        .map(function() {
          return $(this).val();
        }).get();
      var dt_jumlah_barang_qc = $("input[name='dt_jumlah_barang_qc[]']")
        .map(function() {
          return $(this).val();
        }).get();
      var dt_tgl_selesai_qc = $("input[name='dt_tgl_selesai_qc[]']")
        .map(function() {
          return $(this).val();
        }).get();
      var dt_keterangan_qc = $("input[name='dt_keterangan_qc[]']")
        .map(function() {
          return $(this).val();
        }).get();
      var JS_pic_qc = JSON.stringify(dt_pic_qc);
      var JS_nama_barang = JSON.stringify(dt_nama_barang);
      var JS_kode_barang = JSON.stringify(dt_kode_barang);
      var JS_qty = JSON.stringify(dt_qty);
      var JS_koli_karton = JSON.stringify(dt_koli_karton);
      var JS_jumlah_barang_qc = JSON.stringify(dt_jumlah_barang_qc);
      var JS_tgl_selesai_qc = JSON.stringify(dt_tgl_selesai_qc);
      var JS_keterangan_qc = JSON.stringify(dt_keterangan_qc);
      var panjangArray = dt_nama_barang.length;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      // alert(panjangArray);
      if (JS_pic_qc == '' && JS_nama_barang == '' && JS_kode_barang == '' && JS_jumlah == '' && JS_koli_karton == '' && JS_jumlah_barang_qc == '' && JS_tgl_selesai_qc == '' && JS_keterangan_qc == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Harap diisi!'
        });
      } else if (nomor_surat_terima == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nomor Surat Terima harap diisi'
        });
      } else if (nomor_surat_jalan == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nomor Surat Jalan harap diisi'
        });
      } else if (kode_po == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Kode PO Harap dipilih'
        });
      } else if (warehouse == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nama Staff Warehouse harap diisi'
        });
      } else if (JS_pic_qc == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. PIC QC harap dipilih!'
        });
      } else if (JS_nama_barang == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nama Penerima harap dipilih!'
        });
      } else if (JS_kode_barang == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nama SKU harap dipilih!'
        });
      } else if (dt_qty == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nama Kategori harap dipilih!'
        });
      } else if (JS_koli_karton == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nomor Pesanan harus diisi!'
        });
      } else if (JS_tgl_selesai_qc == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. TTD Finance harus diisi!'
        });

      } else {
        $.ajax({
          url: "<?php echo base_url() ?>admin/surat/proses_surat_terima_barang",
          method: "post",
          dataType: 'JSON',
          data: {
            nomor_surat_terima: nomor_surat_terima,
            nomor_surat_jalan: nomor_surat_jalan,
            kode_po: kode_po,
            nama_surat: nama_surat,
            warehouse: warehouse,
            periodik_kirim: periodik_kirim,
            nama_pengirim: nama_pengirim,
            periodik_terima: periodik_terima,
            nama_penerima: nama_penerima,
            dt_pic_qc: JS_pic_qc,
            dt_nama_barang: JS_nama_barang,
            dt_kode_barang: JS_kode_barang,
            dt_qty: JS_qty,
            dt_koli_karton: JS_koli_karton,
            dt_jumlah_barang_qc: JS_jumlah_barang_qc,
            dt_tgl_selesai_qc: JS_tgl_selesai_qc,
            dt_keterangan_qc: JS_keterangan_qc,
            length: panjangArray,
            [csrfName]: csrfHash,
          },
          success: function(data) {
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
              }).then(function() {
                window.location.replace("<?php echo base_url() ?>admin/surat/surat_terima_barang");
              });
            }

          },
          error: function(data) {
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

    $(document).ready(function() {
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
      // barang masuk row

      $('#add_bahan_kemas').click(function() {
        if ($('#in_pic_qc').val() == 'semua') {
          Toast.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan!. Nama PIC harap dipilih!'
          });
        } else {
          var nama_barang = $('#in_nama_barang').val();
          var kode_barang = $('#in_kode_barang').val();
          var qty = $('#in_qty').val();
          var koli_karton = $('#in_koli_karton').val();
          var jumlah_barang_qc = $('#in_jumlah_barang_qc').val();
          var tgl_selesai_qc = $('#in_tgl_selesai_qc').val();
          var keterangan_qc = $('#in_keterangan_qc').val();
          var pic_qc = $('#in_pic_qc').val();
          var pic_qc_teks = $('#in_pic_qc option:selected').text();
          if (nama_barang == '') {
            Toast.fire({
              icon: 'error',
              title: 'Perhatian!',
              text: 'Deskrpsi barang masih kosong!. Silahkan diisi!'
            })
          } else if (kode_barang == 0) {
            Toast.fire({
              icon: 'error',
              title: 'Perhatian!',
              text: 'Kode Barang masih kosong!. Silahkan diisi!'
            })
          } else {
            var html = '<tr>';
            html += '<td> <input type="hidden" id="dt_pic_qc[]" name="dt_pic_qc[]" value="' + pic_qc + '">' + pic_qc_teks + '</td>';
            html += '<td> <input type="hidden" id="dt_nama_barang[]" name="dt_nama_barang[]" value="' + nama_barang + '">' + nama_barang + '</td>';
            html += '<td> <input type="hidden" id="dt_kode_barang[]" name="dt_kode_barang[]" value="' + kode_barang + '">' + kode_barang + '</td>';
            html += '<td> <input type="hidden" id="dt_qty[]" name="dt_qty[]" value="' + qty + '">' + qty + '</td>';
            html += '<td> <input type="hidden" id="dt_koli_karton[]" name="dt_koli_karton[]" value="' + koli_karton + '">' + koli_karton + '</td>';
            html += ' <td> <input type="hidden" id="dt_jumlah_barang_qc[]" name="dt_jumlah_barang_qc[]" value="' + jumlah_barang_qc + '">' + jumlah_barang_qc + '</td>';
            html += '<td> <input type="hidden" id="dt_tgl_selesai_qc[]" name="dt_tgl_selesai_qc[]" value="' + tgl_selesai_qc + '">' + tgl_selesai_qc + ' </td>';
            html += '<td> <input type="hidden" id="dt_keterangan_qc[]" name="dt_keterangan_qc[]" value="' + keterangan_qc + '">' + keterangan_qc + ' </td>';
            html += '<td> <button type="hidden" class="btn btn-danger btn-sm" id="hps_row">Hapus</button> </td> </tr>';

            $('tbody').append(html);

            // clear input data
            $('#in_nama_barang').val('');
            $('#in_kode_barang').val('');
            $('#in_qty').val('');
            $('#in_koli_karton').val('');
            $('#in_jumlah_barang_qc').val('');
            $('#in_tgl_selesai_qc').val('');
            $('#in_keterangan_qc').val('');
            $("#in_pic_qc").val("").trigger("change.select2");

          }

          if ($('#in_nomor_surat_terima_barang').val() == '') {
            $('#in_nomor_surat_terima_barang').val('SPB/' + kode_barang.match(/[\D]*/)[0].split('-')[0] + '<?= $nomor_surat_template ?>');
          }
        }
      });

      $(document).on('click', '#hps_row', function() {
        $(this).closest('tr').remove();
      });

    });


    //Initialize Select2 Elements
    $(document).ready(function() {
      $("#in_periodik_terima").datepicker({
        format: "yyyy/mm/dd"
      }).datepicker("setDate", new Date());
      $("#in_periodik_kirim").datepicker({
        format: "yyyy/mm/dd"
      }).datepicker("setDate", new Date());
      $("#in_tgl_selesai_qc").datepicker({
        format: "yyyy/mm/dd"
      }).datepicker("setDate", new Date());
    });

    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
    $('.select2bs42').select2({
      theme: 'bootstrap4'
    })
  </script>
</div>
<!-- ./wrapper -->

</body>

</html>