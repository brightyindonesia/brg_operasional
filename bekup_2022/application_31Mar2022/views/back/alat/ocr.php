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
      <div class="row">
        <div class="col-sm-2">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Filter OCR</h3>
            </div>

            <div class="box-body">
              <div class="form-group">
                <div class="radio">
                  <label>
                  <input type="radio" name="filter_ocr" id="filter-ocr" value="no" checked>
                    Don't Sync Database
                  </label>
                </div>

                <div class="radio">
                  <label>
                  <input type="radio" name="filter_ocr" id="filter-ocr" value="yes">
                    Sync Database
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-10">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">OCR</h3>
            </div>

            <div class="box-body">
              <div class="form-group"><label>Upload File Image (*)</label>
                  <input type="file" name="file_image" id="file_image" class="form-control" accept="image/png, image/jpg, image/jpeg" multiple="multiple">
              </div>

              <div class="form-group">
                <button name="button" id="ocr_tambah" class="btn btn-success"><i class="fa fa-upload"></i> Upload</button>
              </div>

              <b>Photo Preview</b><br>
              <div id="preview"></div>
              <!-- /.box-body -->
            </div>
          </div>
        </div>  
      </div>

      <div id="message-validasi-tabel"></div>

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>
  <script>
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

    function previewImages() {

      var $preview = $('#preview').empty();
      var validasi = 0;
      if (this.files) $.each(this.files, readAndPreview);

      if (validasi > 0) {
        document.getElementById("file_image").value = null;
        document.getElementById("preview").innerHTML = "";
      }

      function readAndPreview(i, file) {
        
        if (!/\.(jpe?g|png|jpg)$/i.test(file.name)){
          validasi = validasi + 1;
          return alert(file.name +" is not an image");
        } // else...

        if (validasi > 0) {
          document.getElementById("file_image").value = null;
          document.getElementById("preview").innerHTML = "";
        }else{
          var reader = new FileReader();

          $(reader).on("load", function() {
            $preview.append($("<img/>", {src:this.result, height:400}));
          });

          reader.readAsDataURL(file);
        }        
      }

    }

    $('#file_image').on("change", previewImages);

    $('#ocr_tambah').click(function(e){
      e.preventDefault();
      $("#modal-proses").modal('show');
      var photo = document.getElementById('file_image');
      var created = <?php echo $this->session->userdata('id_users'); ?>;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
      csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      if(photo.files['length'] == 0){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. File Gambar harus diisi!'
        });
      }else{
        var formData = new FormData();
        var dataGambar = [];
        var gb = $('#file_image')[0].files;
        for (var i = 0; i < gb.length; i++)
        {
          // dataGambar.push(gb[i]);
          formData.append('photo[]', gb[i]);  
        }             

        // console.log(dataGambar);     
        formData.append([csrfName], csrfHash); 
        formData.append('filter', $('input[type=radio][name=filter_ocr]:checked').attr('value'));   
        $.ajax({ 
          url:"<?php echo base_url()?>admin/alat/proses_ocr",
          method:"post",
          dataType: 'JSON', 
          data:formData,
          contentType: false,
          processData: false,
          success:function(data)  {  
            // alert(data);
            if (data.validasi) {
              $("#modal-proses").modal('hide');
              Toast.fire({
                icon: 'error',
                title: 'Perhatian!',
                text: data.validasi
              })
            }

            if (data.sukses) {
              $("#modal-proses").modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Sukses!',
                text: data.sukses,
              }).then(function(){
                  window.open("<?php echo base_url() ?>admin/alat/export_data_ocr/"+data.filter+"/"+data.nama+"/"+data.hp+"/"+data.resi+"");
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

    // $("#file-input").fileinput({
    //     theme: 'fa',
    //     uploadUrl: '<?php echo base_url()?>admin/alat/proses_orc',
    //     enableResumableUpload: false,
    //     // uploadAsync: false,
    //     resumableUploadOptions: {
    //        // uncomment below if you wish to test the file for previous partial uploaded chunks
    //        // to the server and resume uploads from that point afterwards
    //        // testUrl: '<?php echo base_url()?>admin/alat/proses_orc'
    //     },
    //     uploadExtraData: {
    //         '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>', // for access control / security 
    //     },
    //     allowedFileExtensions: ['jpg', 'png', 'jpeg'],
    //     showCancel: true,
    //     showUpload: true,
    //     showRemove: true,
    //     required: true,
    //     initialPreviewAsData: true, // defaults markup
    //     overwriteInitial: true,
    //     maxFileSize: 1000,
    //     maxFilesNum: 10,
    //     allowedFileTypes: ['image'],
    //     slugCallback: function (filename) {
    //         return filename.replace('(', '_').replace(']', '_');
    //     }
    // });

    // $('#file-input').on('filebatchuploadsuccess', function(event, data) {
    //     var out = '';
    //     $.each(data.files, function(key, file) {
    //         var fname = file.name;
    //         out = out + '<li>' + 'Uploaded file # ' + (key + 1) + ' - '  +  fname + ' successfully.' + '</li>';
    //     });
    //     console.log(out);
    //     // $('#kv-success-2 ul').append(out);
    //     // $('#kv-success-2').fadeIn('slow');
    // });

    // $('#file-input').on('fileuploaded', function(event, data) {
    //     var valURL = [];
    //     var count_files = data.files.length;
    //     var data_ocr = data.response;

    //     var out = '';
    //     $.each(data.files, function(key, file) {
    //         var fname = file.name;
    //         out = out + '<li>' + 'Uploaded file # ' + (key + 1) + ' - '  +  fname + ' successfully.' + '</li>';
    //     });
    //     console.log(out);
    //     // for (var i = 1; i <= 2; i++) {
    //     //     valURL.push(data_ocr.url);
    //     // }
    //     console.log(data_ocr.url);
    //     // Tesseract.recognize(
    //     //   data_ocr.url,
    //     //   'eng'
    //     // ).then(({ data: { text } }) => {
    //     //   valURL.push(data_ocr.url);

    //     //   console.log(text);
    //     //   console.log(valURL);
    //     // })
    // });


    $('#restoredb').click(function(e){
      const Toast = Swal.mixin({
        toast: false,
        position: 'center',
        showConfirmButton: true,
        // confirmButtonColor: '#86ccca',
        // timer: 3000,
        timerProgressBar: false,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })
      e.preventDefault();

      var restore = document.getElementById('restore_db');
      var JS_restore = JSON.stringify(restore.files[0]);
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
      csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      // alert(panjangArray);
      
      if(restore.files['length'] == 0){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. File DB harus diisi!'
        });
      }else{
        var formData = new FormData();
        formData.append('restore_db', $('#restore_db')[0].files[0]);      
        formData.append([csrfName], csrfHash); 

        $("#modal-proses").modal('show');
        $.ajax({ 
          url:"<?php echo base_url()?>admin/keluar/restore_db",
          method:"post",
          dataType: 'JSON', 
          // data:{img: JS_image,vendor:vendor, penerima:penerima, sku: sku, kategori: kategori, ongkir: ongkir, remarks: remarks, nomor_request: nomor_request, dt_id: JS_id, dt_qty: JS_qty, dt_harga: JS_harga, dt_jml: JS_jumlah, dt_diskon: JS_diskon, dt_pajak: JS_pajak, length: panjangArray, [csrfName]: csrfHash},
          data: formData,
          contentType: false,
          processData: false,
          success:function(data)  {  
            // alert(data);
            if (data.validasi) {
              $("#modal-proses").modal('hide');
              Toast.fire({
                icon: 'error',
                title: 'Perhatian!',
                text: data.validasi
              })
            }

            if (data.sukses) {
              $("#modal-proses").modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Sukses!',
                text: data.sukses,
              }).then(function(){
                window.location.replace("<?php echo base_url()?>admin/dashboard");
              });
            }
            
          },
          error: function(data){
            console.log(data.responseText);
          } 
        });
      }
    });
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
