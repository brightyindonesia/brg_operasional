<?php $this->load->view('back/template/meta'); ?>
<div class="wrapper">

  <?php $this->load->view('back/template/navbar'); ?>
  <?php $this->load->view('back/template/sidebar'); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><?php echo $page_title.": ".$provinsi->nama_provinsi ?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><?php echo $module_provinsi ?></li>
        <li class="active"><?php echo $page_title ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <?php if($this->session->flashdata('message')){echo $this->session->flashdata('message');} ?>
      <?php echo validation_errors() ?>
      <div class="box box-primary">
        <?php 
          // echo form_open($action) 
        ?>
          <div class="box-body">
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group"><label>Nama Kota / Kabupaten (*)</label>
                  <?php echo form_input($kotkab_nama) ?>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="form-group"><label>Keyword Kota / Kabupaten: (*)</label>
                  <br>
                    <?php 
                        // if ($arr_keys == '') {
                        //   $keys = '';
                        // }else{
                        //   $keys = implode(",", $arr_keys);
                        // }
                        echo form_input($keys_kotkab) 
                    ?>
                </div>
              </div>
            </div>
          </div>
          <?php echo form_input($id_keyword_provinsi, $provinsi->id_keyword_provinsi) ?>
          <?php echo form_input($id_keyword_detail_provinsi) ?>
          <div class="box-footer">
            <button type="submit" id="btn-simpan" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
            <a href="<?php echo base_url('admin/keyword/provinsi') ?>" class="btn btn-info"><i class="fa fa-table"></i> Back to Provinsi Data</a>
          </div>
          <!-- /.box-body -->
        <!-- <?php echo form_close() ?> -->
      </div>

      <div class="box no-border">
        <div class="box-header with-border">
          <h3 class="box-title">City and County List: <?php echo $provinsi->nama_provinsi ?></h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table id="table-kabupaten" class="table no-margin">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Nama Kota / Kabupaten</th>
                  <th>Keyword Kota / Kabupaten</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                
                  <?php 
                    $no = 1;
                    foreach ($detail_provinsi as $val_detail) {
                  ?>
                  <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $val_detail->nama_kotkab; ?></td>
                    <td>
                      <?php 
                        $this->lib_keyword->result_detail_keys_provinsi_by_id_detail_provinsi($val_detail->id_detail_keyword_provinsi)
                      ?>
                    </td>
                    <td>
                      <button onclick="edit_detail_provinsi(<?php echo $val_detail->id_detail_keyword_provinsi ?>)" class="btn btn-warning btn-sm" >Ubah</button>
                    </td>
                  </tr>
                  <?php
                      $no++;
                    }
                  ?>
                
              </tbody>
            </table>
          </div>
          <!-- /.table-responsive -->
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php 
    include('modal/modal_edit_detail_provinsi.php');
  ?>

  <?php $this->load->view('back/template/footer'); ?>
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables-bs/css/dataTables.bootstrap.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>datatables/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables-bs/js/dataTables.bootstrap.min.js"></script>
  <script>
    $('#table-kabupaten').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "pageLength": 50,
      "ordering": true,
      "info": true,
      "autoWidth": true
    });

    $("#keys-kotkab").tagsInput();
    

    $('#modal-edit-provinsi').on('hidden.bs.modal', function () {
        $('#edit-kotkab').val('');
    });

    function edit_detail_provinsi(id) {
      if ($("#modal-edit-provinsi").modal("show")) {
        $("#edit-keyword-kotkab").tagsInput();
        $('#edit-kotkab').val('');
        $.getJSON('<?php echo base_url('admin/keyword/get_detail_provinsi_by_id_detail_provinsi/') ?>'+id+'', function(data){
            if(data){
                $('#edit-id').val(data.id);
                $('#edit-provinsi').val(data.id_provinsi);
                $('#edit-kotkab').val(data.nama_kotkab);
                $('#edit-keyword-kotkab').importTags(data.keys_kotkab);
            }
        });
      }
    }

    $('#edit-simpan').click(function(){
        $('#edit-pilihan').val('simpan');
        $('#form-edit-detail-provinsi').submit();
    });

    $('#edit-hapus').click(function(){
        var result = confirm("Are you sure?");
        if (result) {
            $('#edit-pilihan').val('hapus');
            $('#form-edit-detail-provinsi').submit();
        }
    });

    $('#form-edit-detail-provinsi').submit(function(e){
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

        var id = document.getElementById('edit-id').value;
        var id_provinsi = document.getElementById('edit-provinsi').value;
        var pilihan = document.getElementById('edit-pilihan').value;
        var kotkab = document.getElementById('edit-kotkab').value;
        var keys = document.getElementById('edit-keyword-kotkab').value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

        $.ajax({
            url:"<?php echo base_url()?>admin/keyword/detail_provinsi_ubah",
            method:"POST",
            dataType: 'JSON',
            data:{ id: id, id_provinsi: id_provinsi, pilihan: pilihan, kotkab: kotkab, keys: keys,[csrfName]: csrfHash },
            success:function(data)  {  
            // console.log(data);
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
                window.location.replace("<?php echo base_url()?>admin/keyword/provinsi_ubah/"+data.id);
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
    });

    $('#btn-simpan').click(function(e){
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

        var keys = document.getElementById('keys-kotkab').value;
        var kotkab = document.getElementById('nama-kotkab').value;
        var id = document.getElementById('id-keyword-provinsi').value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

        if (keys == '') {
          Toast.fire({
            icon: 'error',
            title: 'Perhatian!',
            text: 'Keyword Kabupaten Tidak Boleh Kosong!'
          })
        }else{
          $.ajax({ 
              url:"<?php echo base_url()?>admin/keyword/provinsi_ubah_proses",
              method:"post",
              dataType: 'JSON', 
              data:{keys: keys, kotkab:kotkab, id:id, [csrfName]: csrfHash},
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
                    window.location.replace("<?php echo base_url()?>admin/keyword/provinsi_ubah/"+data.id);
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
