<?php date_default_timezone_set("Asia/Jakarta");
defined("BASEPATH") or exit("No direct script access allowed"); 

class Membership_model extends CI_Model
{
    public $table = "membership";
    public $id = "id_membership";
    public $order = "DESC";

    function get_all()
    {
        $this->db->order_by('min_poin', 'ASC');
        return $this->db->get($this->table)->result();
    }

    function get_all_combobox()
    {
        $this->db->order_by("min_poin", 'ASC');
        $data = $this->db->get($this->table);

        if ($data->num_rows() > 0) {
            foreach ($data->result_array() as $row) {
                $result[""] = "- Pilih Membership -";
                $result[$row["tier"]] = $row["tier"];
            }
            return $result;
        }
    }

    function get_by_id($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->get($this->table)->row();
    }

    function total_rows()
    {
        return $this->db->get($this->table)->num_rows();
    }

    function insert($data)
    {
        $this->db->insert($this->table, $data);
    }

    function update($id, $data)
    {
        $this->db->where($this->id, $id);
        $this->db->update($this->table, $data);
    }

    function delete($id)
    {
        $this->db->where($this->id, $id);
        $this->db->delete($this->table);
    }

    function getTierPoinByTotalPoin($total_order)
    {
        $this->db->select("tier, x_poin");
        $this->db->where("min_poin >=", $total_order);
        $this->db->or_where("max_poin >", $total_order);
        $this->db->limit(1);
        return $this->db->get($this->table)->row();
    }

    function get_datatable_membership_insight($tier)
    {
        $this->db->select("min_poin, max_poin");
        if ($tier != "") {
            $this->db->where("tier", $tier);
        }
        $this->db->limit(1);
        $membership = $this->db->get($this->table)->row();
        // die(print_r($membership));

        $this->db->select(
            "*, SUM(QTY) as total_qty, COUNT(penjualan.nomor_pesanan) as jumlah_pesanan, SUM(harga_jual) as total_harga_jual"
        );
        $this->db->join(
            "detail_penjualan",
            "penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan"
        );

        $start =
            isset($_GET["start"]) && $_GET["start"] != null
                ? $_GET["start"]
                : 0;
        $length =
            isset($_GET["length"]) && $_GET["length"] != null
                ? $_GET["length"]
                : 50;

        $this->db->having("(SUM(harga_jual)/10000) >=", $membership->min_poin);
        $this->db->having("(SUM(harga_jual)/10000) <", $membership->max_poin);

        $this->db->group_by("nama_penerima, hp_penerima");
        $this->db->limit($length, $start);
        $this->db->order_by("total_qty", "desc");

        $customer = $this->db->get("penjualan")->result();
        $dataJSON = [];
        $no = 1;
        foreach ($customer as $data) {
            $row = [];
            $row["no"] = $no++;
            $row["nama_penerima"] = $data->nama_penerima;
            $row["hp_penerima"] = $data->hp_penerima;
            $row["total_harga_jual"] =
                "Rp. " . number_format($data->total_harga_jual, 0, ",", ".");
            $row["tier"] = $this->Membership_model->getTierPoinByTotalPoin(
                $data->total_harga_jual / 10000
            )
                ? $this->Membership_model->getTierPoinByTotalPoin(
                    $data->total_harga_jual / 10000
                )->tier
                : "";
            $row["poin"] = $data->total_harga_jual / 10000;
            $dataJSON[] = $row;
        }
        return $output = [
            "recordsTotal" => 10,
            "recordsFiltered" => 10,
            "data" => $dataJSON,
        ];
    }

    function get_count_membership_by_id($year, $tier)
    {
        $this->db->select("min_poin, max_poin");
        if ($tier != "") {
            $this->db->where($this->id, $tier);
        }
        $this->db->limit(1);
        $membership = $this->db->get($this->table)->row();

        // $this->db->distinct();
        // $this->db->select('hp_penerima');
        $this->db->join(
            "detail_penjualan",
            "penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan"
        );

        $this->db->group_by("nama_penerima, hp_penerima");
        $this->db->having("(SUM(harga_jual)/10000) >=", $membership->min_poin);
        $this->db->having("(SUM(harga_jual)/10000) <", $membership->max_poin);
        $this->db->where("tgl_penjualan >=", date('Y-m-d', mktime(false, false, false, 1, 1, $year)));
        $this->db->where("tgl_penjualan <=", date('Y-m-d',mktime(false, false, false, 12, 1, $year)));

        // $q = $this->db->query("SELECT (SELECT COUNT(nomor_pesanan) FROM penjualan WHERE YEAR(tgl_penjualan) = $year) as total_count FROM `penjualan` JOIN `detail_penjualan` ON `penjualan`.`nomor_pesanan` = `detail_penjualan`.`nomor_pesanan` WHERE YEAR(tgl_penjualan) = $year GROUP BY nama_penerima, hp_penerima HAVING (SUM(harga_jual)/10000) >= $membership->min_poin AND (SUM(harga_jual)/10000) < $membership->max_poin")->row();
        return $this->db->get("penjualan")->num_rows();
        // die(print_r($this->db->query("SELECT hp_penerima FROM penjualan WHERE tgl_penjualan >= '2023-1-1' AND tgl_penjualan <= '2023-12-31' GROUP BY nama_penerima, hp_penerima HAVING (SUM(harga_jual)/10000) >= 5 AND (SUM(harga_jual)/10000) < 100")->num_rows()));
    }

    function get_data_every_month_by_year($year, $tier)
    {
        $this->db->select("min_poin, max_poin");
        if ($tier != "") {
            $this->db->where($this->id, $tier);
        }
        $this->db->limit(1);
        $membership = $this->db->get($this->table)->row();

        $data = [];
        $i = 0;
        for ($m = 1; $m <= 12; $m++) {
            $month = mktime(false, false, false, $m, 1, $year);
            // echo $month. '<br>';
            // $q = $this->db->query("SELECT (SELECT COUNT(nomor_pesanan) FROM penjualan WHERE YEAR(tgl_penjualan) = $year AND MONTH(tgl_penjualan) = $m) as total_count FROM `penjualan` JOIN `detail_penjualan` ON `penjualan`.`nomor_pesanan` = `detail_penjualan`.`nomor_pesanan` WHERE YEAR(tgl_penjualan) = $year AND MONTH(tgl_penjualan) = $m GROUP BY nama_penerima, hp_penerima HAVING (SUM(harga_jual)/10000) >= $membership->min_poin AND (SUM(harga_jual)/10000) < $membership->max_poin")->row();
            // $this->db->distinct();
            // $this->db->select('hp_penerima');
            $this->db->join(
                "detail_penjualan",
                "penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan"
            );

            // $this->db->where("MONTH(tgl_penjualan)", date('m', $month));
            // $this->db->where("YEAR(tgl_penjualan)", date('Y', $month));
            $this->db->where('DATE_FORMAT(tgl_penjualan, "%Y%m") = ', date('Ym', strtotime('01-' . date('m', $month) . '-' . $year)));
            $this->db->group_by("nama_penerima, hp_penerima");
            // $this->db->group_by('MONTH(created), YEAR(created)');
            $this->db->having("(SUM(harga_jual)/10000) >=", $membership->min_poin);
            $this->db->having("(SUM(harga_jual)/10000) <", $membership->max_poin);

            // die(print_r($this->db->get("penjualan")->result()));
            // return $this->db->get("penjualan")->num_rows();
            $data[$i][] = $month * 1000;
            $data[$i][] = $this->db->get("penjualan")->num_rows();
            // $data[$i][] = $q ? (int) $q->total_count : 0;
            $i++;
        }
        // die(print_r($this->db->query('SELECT DISTINCT hp_penerima FROM penjualan WHERE MONTH(tgl_penjualan) = 1 AND YEAR(tgl_penjualan) = 2022 GROUP BY nama_penerima,hp_penerima HAVING (SUM(harga_jual)/10000) >= 5 AND (SUM(harga_jual)/10000) < 100')->num_rows()));
        return $data;
    }
}
