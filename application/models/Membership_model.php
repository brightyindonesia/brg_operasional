<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Membership_model extends CI_Model
{
    public $table = "membership";
    public $id = "id_membership";
    public $order = "DESC";

    function get_all()
    {
        return $this->db->get($this->table)->result();
    }

    function get_all_combobox()
    {
        $this->db->order_by("tier");
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

    function getTierPoinByTotalOrder($total_order)
    {
        $this->db->select("tier, x_poin");
        $this->db->where("min_belanja >=", $total_order);
        $this->db->or_where("max_belanja >", $total_order);
        $this->db->limit(1);
        return $this->db->get($this->table)->row();
    }

    function get_datatable_membership_insight($tier)
    {
        $this->db->select("min_belanja, max_belanja");
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

        $this->db->having("SUM(harga_jual) >=", $membership->min_belanja);
        $this->db->having("SUM(harga_jual) <", $membership->max_belanja);

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
            $row["tier"] = $this->Membership_model->getTierPoinByTotalOrder(
                $data->total_harga_jual
            )
                ? $this->Membership_model->getTierPoinByTotalOrder(
                    $data->total_harga_jual
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
        $this->db->select("min_belanja, max_belanja");
        if ($tier != "") {
            $this->db->where($this->id, $tier);
        }
        $this->db->limit(1);
        $membership = $this->db->get($this->table)->row();

        $this->db->join(
            "detail_penjualan",
            "penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan"
        );

        $this->db->having("SUM(harga_jual) >=", $membership->min_belanja);
        $this->db->having("SUM(harga_jual) <", $membership->max_belanja);
        $this->db->where("YEAR(created)", $year);

        $this->db->group_by("nama_penerima, hp_penerima");
        return $this->db->get("penjualan")->num_rows();
    }

    function get_data_every_month_by_year($year, $tier)
    {
        $this->db->select("min_belanja, max_belanja");
        if ($tier != "") {
            $this->db->where($this->id, $tier);
        }
        $this->db->limit(1);
        $membership = $this->db->get($this->table)->row();

        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $month = strtotime(date("F Y", gmmktime(0, 0, 0, $m, 1, $year)));
            // echo $month. '<br>';
            $this->db->join(
                "detail_penjualan",
                "penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan"
            );

            $this->db->having("SUM(harga_jual) >=", $membership->min_belanja);
            $this->db->having("SUM(harga_jual) <", $membership->max_belanja);
            $this->db->where("MONTH(created)", date('m', $month));
            $this->db->where("YEAR(created)", $year);

            $this->db->group_by("nama_penerima, hp_penerima");
            // die(print_r($this->db->get("penjualan")->result()));
            // return $this->db->get("penjualan")->num_rows();
            $data[(int)$m-1][] = $month * 1000;
            $data[(int)$m-1][] = $this->db->get("penjualan")->num_rows();
        }
        return $data;
    }
}
