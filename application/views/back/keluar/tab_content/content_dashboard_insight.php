<div class="row">
  <div class="col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-aqua">
        <i class="fa fa-file"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Jumlah Invoice</span>
        <span class="info-box-number" id="jumlah-invoice"> <?= number_format($jumlah_invoice,0,',','.')  ?> </span>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-yellow">
        <i class="fa fa-hashtag"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Jumlah Qty</span>
        <span class="info-box-number" id="jumlah-qty"> <?=$qty_harga->qty ? number_format($qty_harga->qty,0,',','.') : 0?> </span>
      </div>
    </div>
  </div>
  <!-- /.info-box -->
    <div class="col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-blue">
        <i class="fa fa-money"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Avg. Order Value</span>
        <span class="info-box-number" id="avg-order-value"> Rp. <?=$qty_harga->total_harga ? number_format($qty_harga->total_harga,0,',','.') : 0 ?> </span>
      </div>
    </div>
</div>
    <!-- /.info-box -->
  <div class="col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-green">
        <i class="fa fa-bar-chart"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Avg. Order Number</span>
        <span class="info-box-number" id="avg_order_number"> <?= $avg_order_number->avg_order_number ? round($avg_order_number->avg_order_number,2) : 0 ?> </span>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-indigo">
        <i class="fa fa-users"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Jumlah Pembeli</span>
        <span class="info-box-number" id="jumlah-pembeli"> <?= $jumlah_pembeli ? number_format($jumlah_pembeli,0,',','.') : 0 ?> </span>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-purple">
        <i class="fa fa-user"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Pembeli Baru</span>
        <span class="info-box-number" id="jumlah-pembeli"> <?= $pembeli_baru ? number_format($pembeli_baru,0,',','.') : 0 ?> </span>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-teal">
        <i class="fa fa-shopping-cart"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Jumlah Pembeli Repeat Order</span>
        <span class="info-box-number" id="jumlah-pembeli"> <?= $pembeli_repeat_order ? $pembeli_repeat_order : 0 ?> </span>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="info-box">
      <span class="info-box-icon bg-orange">
        <i class="fa fa-repeat"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Repeat Order</span>
        <span class="info-box-number" id="jumlah-pembeli"> <?= $repeat_order ? round($repeat_order, 2) : 0 ?>% </span>
      </div>
    </div>
  </div>