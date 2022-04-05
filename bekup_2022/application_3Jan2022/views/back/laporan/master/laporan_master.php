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
      <!-- BARIS DATA MASTER -->
      <div class="row">
        <div class="col-sm-3">
          <!-- TABLE: LAPORAN PRODUK -->
          <div class="box box-info collapsed-box">
            <div class="box-header with-border">
              <h3 class="box-title">Produk</h3>
              <div class="box-tools pull-right">
                <a href="#" class="btn btn-xs btn-success"><i class="fa fa-file-pdf-o" style="margin-right: 5px"></i> Semua Data</a>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
              </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label>Berdasarkan SKU</label>
                    <button class="btn btn-xs btn-success" style="float: right;"><i class="fa fa-file-pdf-o" style="margin-right: 5px"></i> Cetak</button>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label>Berdasarkan Toko</label>
                    <button class="btn btn-xs btn-success" style="float: right;"><i class="fa fa-file-pdf-o" style="margin-right: 5px"></i> Cetak</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->  
        </div>

        <div class="col-sm-3">
          <!-- TABLE: LAPORAN BAHAN KEMAS -->
          <div class="box box-info collapsed-box">
            <div class="box-header with-border">
              <h3 class="box-title">Bahan Kemas</h3>
              <div class="box-tools pull-right">
                <a href="#" class="btn btn-xs btn-success"><i class="fa fa-file-pdf-o" style="margin-right: 5px"></i> Semua Data</a>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
              </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->  
        </div>
      </div>

      <!-- BARIS DATA TRANSAKSI -->
      <div class="row">
        <div class="col-sm-3">
          <!-- TABLE: LAPORAN TIMELINE PRODUKSI -->
          <div class="box box-success collapsed-box">
            <div class="box-header with-border">
              <h3 class="box-title">Timeline Produksi</h3>
              <div class="box-tools pull-right">
                <a href="#" class="btn btn-xs btn-success"><i class="fa fa-file-pdf-o" style="margin-right: 5px"></i> Semua Data</a>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
              </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->  
        </div>

        <div class="col-sm-3">
          <!-- TABLE: LAPORAN TIMELINE BAHAN PRODUKSI -->
          <div class="box box-success collapsed-box">
            <div class="box-header with-border">
              <h3 class="box-title">Timeline Bahan Produksi</h3>
              <div class="box-tools pull-right">
                <a href="#" class="btn btn-xs btn-success"><i class="fa fa-file-pdf-o" style="margin-right: 5px"></i> Semua Data</a>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
              </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->  
        </div>

        <div class="col-sm-3">
          <!-- TABLE: LAPORAN PENJUALAN -->
          <div class="box box-success collapsed-box">
            <div class="box-header with-border">
              <h3 class="box-title">Penjualan</h3>
              <div class="box-tools pull-right">
                <a href="#" class="btn btn-xs btn-success"><i class="fa fa-file-pdf-o" style="margin-right: 5px"></i> Semua Data</a>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
              </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->  
        </div>

        <div class="col-sm-3">
          <!-- TABLE: LAPORAN RESI -->
          <div class="box box-success collapsed-box">
            <div class="box-header with-border">
              <h3 class="box-title">Resi</h3>
              <div class="box-tools pull-right">
                <a href="#" class="btn btn-xs btn-success"><i class="fa fa-file-pdf-o" style="margin-right: 5px"></i> Semua Data</a>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
              </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->  
        </div>

        <div class="col-sm-3">
          <!-- TABLE: LAPORAN SURAT JALAN -->
          <div class="box box-success collapsed-box">
            <div class="box-header with-border">
              <h3 class="box-title">Surat Jalan</h3>
              <div class="box-tools pull-right">
                <a href="#" class="btn btn-xs btn-success"><i class="fa fa-file-pdf-o" style="margin-right: 5px"></i> Semua Data</a>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
              </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->  
        </div>

        <div class="col-sm-3">
          <!-- TABLE: LAPORAN SURAT PL -->
          <div class="box box-success collapsed-box">
            <div class="box-header with-border">
              <h3 class="box-title">Surat Packing List</h3>
              <div class="box-tools pull-right">
                <a href="#" class="btn btn-xs btn-success"><i class="fa fa-file-pdf-o" style="margin-right: 5px"></i> Semua Data</a>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
              </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->  
        </div>

        <div class="col-sm-3">
          <!-- TABLE: LAPORAN RFQ -->
          <div class="box box-success collapsed-box">
            <div class="box-header with-border">
              <h3 class="box-title">Request For Quotation</h3>
              <div class="box-tools pull-right">
                <a href="#" class="btn btn-xs btn-success"><i class="fa fa-file-pdf-o" style="margin-right: 5px"></i> Semua Data</a>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
              </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->  
        </div>

        <div class="col-sm-3">
          <!-- TABLE: LAPORAN RFQ -->
          <div class="box box-success collapsed-box">
            <div class="box-header with-border">
              <h3 class="box-title">Purchase Order</h3>
              <div class="box-tools pull-right">
                <a href="#" class="btn btn-xs btn-success"><i class="fa fa-file-pdf-o" style="margin-right: 5px"></i> Semua Data</a>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
              </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->  
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables-bs/css/dataTables.bootstrap.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>datatables/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables-bs/js/dataTables.bootstrap.min.js"></script>
  <script>

  </script>

</div>
<!-- ./wrapper -->

</body>
</html>
