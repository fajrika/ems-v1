<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/phpspreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
class Status_tagihan extends CI_Controller {
    function __construct() {
        parent::__construct();

        $this->load->model('m_login');
        if(!$this->m_login->status_login()) redirect(site_url());

        $this->load->model('m_core');
        global $jabatan,$project,$menu;
        $jabatan = $this->m_core->jabatan();
        $project = $this->m_core->project();
        $menu = $this->m_core->menu();

        $this->load->model('report/m_status_tagihan');
    }
    public function index()
    {
        $isi = array();
        $isi['load_css'] = load_css(['select2','datetimepicker']);
        $isi['load_js'] = load_js(['select2','moment','datetimepicker']);
        $isi['kawasan'] = $this->m_status_tagihan->get_kawasan();
        $isi['jenis'] = $this->m_status_tagihan->get_service();
        $isi['is_service'] = $this->db->where("service.project_id", $GLOBALS['project']->id)->where("service.delete", 0)->get('service');
        $this->view('normal', 'Report > Status Tagihan', 'Report Status Tagihan', 'proyek/report/status_tagihan/view', $isi);
        $this->load->view('core/css_custom_master');
    }

    public function generate()
    {
        ini_set('memory_limit', '-1'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
        ini_set('sqlsrv.ClientBufferMaxKBSize', '524288'); // Setting to 512M
        ini_set('pdo_sqlsrv.client_buffer_max_kb_size', '524288'); // Setting to 512M - for pdo_sqlsrv
        ini_set('max_execution_time','-1'); // Setting to 512M - for pdo_sqlsrv
        $variable  = "?kawasan=".$this->input->post("kawasan");
        $variable .= "&blok=".$this->input->post("blok");
        $variable .= "&jenis_service=".$this->input->post("jenis_service");
        $variable .= "&periode_awal=".$this->input->post("periode_awal");
        $variable .= "&periode_akhir=".$this->input->post("periode_akhir");
        $variable .= "&status_tagihan=".$this->input->post("status_tagihan");

        echo json_encode(array(
            'status'=>1,
            'link_data'=>site_url('report/status_tagihan/load_table'.$variable)
        ));
    }
    public function get_data_ajax()
    {
        $req = array(
            'project_id' => $GLOBALS['project']->id,
            'kawasan' => ($this->input->post('kawasan')=='all' ? '' : $this->input->post('kawasan')),
            'blok' => ($this->input->post('blok')=='all' ? '' : $this->input->post('blok')),
            'periode_awal' => (empty($this->input->post('periode_awal')) ? '' : date('Y-m', strtotime($this->input->post('periode_awal'))).'-01'),
            'periode_akhir' => (empty($this->input->post('periode_akhir')) ? '' : date("Y-m-d", strtotime($this->input->post('periode_akhir') . "-01 +1 Month"))),
            'status_tagihan' => ($this->input->post('status_tagihan')=='all' ? '' : $this->input->post('status_tagihan')),
            'jenis_service' => ($this->input->post('jenis_service')=='all' ? '' : $this->input->post('jenis_service')),
        );

        if (!empty($req['jenis_service']))
        {
            if ($req['jenis_service']=='1')
            {
                $data = $this->m_status_tagihan->get_tagihan_lingkungan($req)->result_array();
            }
            else
            {
                $data = $this->m_status_tagihan->get_tagihan_air($req)->result_array();
            }
        }
        else
        {
            $data = array_merge(
                        $this->m_status_tagihan->get_tagihan_air($req)->result_array(), 
                        $this->m_status_tagihan->get_tagihan_lingkungan($req)->result_array()
                    );
        }

        $output = array(
            'status' => 1,
            'table' => array()
        );

        if ($data) 
        {
            foreach ($data as $r)
            {
                $r['penghuni_id'] = (empty($r['penghuni_id']) ? 'Belum Dihuni' : 'Sudah Dihuni');
                if ($r['status_tagihan']=='1')
                {
                    $r['status_tagihan'] = 'Terbayar';
                }
                else if ($r['status_tagihan']=='0')
                {
                    $r['status_tagihan'] = 'Belum Terbayar';
                }
                else if ($r['status_tagihan']=='4')
                {
                    $r['status_tagihan'] = 'Belum Lunas';
                }
                array_push($output['table'], array(
                    $r['kawasan_name'],
                    $r['blok_name'],
                    $r['no_unit'],
                    $r['periode'],
                    $r['service_jenis'],
                    $r['luas_tanah'],
                    $r['luas_bangunan'],
                    $r['pemilik_name'],
                    $r['pemilik_address'],
                    $r['pemilik_mobilephone1'],
                    $r['pemilik_mobilephone2'],
                    $r['pemilik_homephone'],
                    $r['penghuni_id'],
                    $r['status_tagihan'],
                ));
            }
        }
        echo json_encode($output);
    }

    public function load_table()
    {
        $req = array(
            'project_id' => $GLOBALS['project']->id,
            'kawasan' => ($this->input->get('kawasan')=='all' ? '' : $this->input->get('kawasan')),
            'blok' => ($this->input->get('blok')=='all' ? '' : $this->input->get('blok')),
            'periode_awal' => (empty($this->input->get('periode_awal')) ? '' : date('Y-m', strtotime($this->input->get('periode_awal'))).'-01'),
            'periode_akhir' => (empty($this->input->get('periode_akhir')) ? '' : date("Y-m-d", strtotime($this->input->get('periode_akhir') . "-01 +1 Month"))),
            'status_tagihan' => ($this->input->get('status_tagihan')=='all' ? '' : $this->input->get('status_tagihan')),
            'jenis_service' => ($this->input->get('jenis_service')=='all' ? '' : $this->input->get('jenis_service')),
        );

        if (!empty($req['jenis_service']))
        {
            if ($req['jenis_service']=='1')
            {
                $data = $this->m_status_tagihan->get_tagihan_lingkungan($req)->result_array();
            }
            else
            {
                $data = $this->m_status_tagihan->get_tagihan_air($req)->result_array();
            }
        }
        else
        {
            $data = array_merge(
                        $this->m_status_tagihan->get_tagihan_air($req)->result_array(), 
                        $this->m_status_tagihan->get_tagihan_lingkungan($req)->result_array()
                    );
        }

        if ($data) 
        {
            $periode_array = array();

            foreach ($data as $r)
            {
                if (!in_array($r['periode'], $periode_array))
                {
                    array_push($periode_array, $r['periode']);
                }
            }
            for ($i=0; $i < count($periode_array); $i++)
            { 
                echo "<tr><td colspan='13' style='background-color: #c1ffe9; font-weight: 600;'>Periode  - ".date('d/m/Y', strtotime($periode_array[$i]))."</td></tr>";
                foreach ($data as $r)
                {
                    if ($r['periode'] == $periode_array[$i])
                    {
                        ?>
                            <tr style="background-color: #ffffff;">
                                <td><?= $r['kawasan_name']; ?></td>
                                <td><?= $r['blok_name']; ?></td>
                                <td><?= $r['no_unit']; ?></td>
                                <td><?= $r['luas_tanah']; ?></td>
                                <td><?= $r['luas_bangunan']; ?></td>
                                <td><?= $r['pemilik_name']; ?></td>
                                <td><?= $r['pemilik_address']; ?></td>
                                <td><?= $r['pemilik_mobilephone1']; ?></td>
                                <td><?= $r['pemilik_mobilephone2']; ?></td>
                                <td><?= $r['pemilik_homephone']; ?></td>
                                <td><?= (empty($r['penghuni_id']) ? 'Belum Dihuni' : 'Sudah Dihuni'); ?></td>
                                <td><?= $r['service_jenis']; ?></td>
                                <td><?= ($r['status_tagihan'] ? 'Terbayar' : 'Belum Terbayar'); ?></td>
                            </tr>
                        <?php
                    }
                }
            }
        }
        else
        {
            echo "<tr><td colspan='13' align='center'>No data available in table</td></tr>";
        }
    }

    // function for export to excel
    public function export_excel()
    {
        $spreadsheet   = new Spreadsheet();
        $sheet         = $spreadsheet->getActiveSheet();
        $filename      = $GLOBALS['project']->code . '_Status_Tagihan_' . date('YmdHis');

        $req = array(
            'project_id' => $GLOBALS['project']->id,
            'kawasan' => ($this->input->get('kawasan')=='all' ? '' : $this->input->get('kawasan')),
            'blok' => ($this->input->get('blok')=='all' ? '' : $this->input->get('blok')),
            'periode_awal' => (empty($this->input->get('periode_awal')) ? '' : date('Y-m', strtotime($this->input->get('periode_awal'))).'-01'),
            'periode_akhir' => (empty($this->input->get('periode_akhir')) ? '' : date("Y-m-d", strtotime($this->input->get('periode_akhir') . "-01 +1 Month"))),
            'status_tagihan' => ($this->input->get('status_tagihan')=='all' ? '' : $this->input->get('status_tagihan')),
            'jenis_service' => ($this->input->get('jenis_service')=='all' ? '' : $this->input->get('jenis_service')),
        );

        $styleJudul  = [
            'font' => [
                'color' => [
                    'rgb' => 'FFFFFF'
                ],
                'bold'=>true,
                'size'=>11
            ],
            'fill'=>[
                'fillType' =>  fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '31869B'
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ];

        $styleBorder = [
            'borders' => [
                'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,],
                'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,],
                'right' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,],
                'left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,],
            ],
        ];

        // STYLE judul table
        // $spreadsheet->getActiveSheet()->getStyle('A1:L4')->applyFromArray($styleJudul);

        // style lebar kolom
            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(17);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(13);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(13);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(40);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(18);
            $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(17);
            $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(17);
            $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(17);
            $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(17);

        //Style Judul table
            $spreadsheet->getActiveSheet()->setCellValue('A1', "Status Tagihan")->mergeCells("A1:N1")
                        ->getStyle('A1:N1')->applyFromArray($styleBorder);
            $spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('A2', "Kawasan")
                        ->getStyle('A2')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('B2', "Blok")
                        ->getStyle('B2')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('C2', "Unit")
                        ->getStyle('C2')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('D2', "Periode")
                        ->getStyle('D2')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('E2', "Jenis Service")
                        ->getStyle('E2')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('F2', "Luas Tanah")
                        ->getStyle('F2')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('G2', "Luas Bangunan")
                        ->getStyle('G2')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('H2', "Nama Pemilik")
                        ->getStyle('H2')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('I2', "Alamat")
                        ->getStyle('I2')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('J2', "Tlp. 1")
                        ->getStyle('J2')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('K2', "Tlp. 2")
                        ->getStyle('K2')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('L2', "Handphone")
                        ->getStyle('L2')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('M2', "Status Huni")
                        ->getStyle('M2')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->setCellValue('N2', "Status Bayar")
                        ->getStyle('N2')->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


        if (!empty($req['jenis_service']))
        {
            if ($req['jenis_service']=='1')
            {
                $data = $this->m_status_tagihan->get_tagihan_lingkungan($req)->result_array();
            }
            else
            {
                $data = $this->m_status_tagihan->get_tagihan_air($req)->result_array();
            }
        }
        else
        {
            $data = array_merge(
                        $this->m_status_tagihan->get_tagihan_air($req)->result_array(), 
                        $this->m_status_tagihan->get_tagihan_lingkungan($req)->result_array()
                    );
        }

        $nomor = 3;
        if ($data) 
        {
            // $periode_array = array();

            // foreach ($data as $r)
            // {
            //     if (!in_array($r['periode'], $periode_array))
            //     {
            //         array_push($periode_array, $r['periode']);
            //     }
            // }
            // for ($i=0; $i < count($periode_array); $i++)
            // { 
                // $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, "Periode  - ".date('d/m/Y', strtotime($periode_array[$i])))->mergeCells("A".$nomor.":N".$nomor)->getStyle("A".$nomor.":N".$nomor)->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                // $nomor++;
                foreach ($data as $r)
                {
                    // if ($r['periode'] == $periode_array[$i])
                    // {
                        $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, $r['kawasan_name'], 0)
                                    ->getStyle('A'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('B'.$nomor, $r['blok_name'], 0)
                                    ->getStyle('B'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('C'.$nomor, $r['no_unit'], 0)
                                    ->getStyle('C'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('D'.$nomor, $r['periode'], 0)
                                    ->getStyle('D'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('E'.$nomor, $r['service_jenis'], 0)
                                    ->getStyle('E'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('F'.$nomor, $r['luas_tanah'], 0)
                                    ->getStyle('F'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('G'.$nomor, $r['luas_bangunan'], 0)
                                    ->getStyle('G'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('H'.$nomor, $r['pemilik_name'], 0)
                                    ->getStyle('H'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('I'.$nomor, $r['pemilik_address'], 0)
                                    ->getStyle('I'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('J'.$nomor, $r['pemilik_mobilephone1'], 0)
                                    ->getStyle('J'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('K'.$nomor, $r['pemilik_mobilephone2'], 0)
                                    ->getStyle('K'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('L'.$nomor, $r['pemilik_homephone'], 0)
                                    ->getStyle('L'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('M'.$nomor, (empty($r['penghuni_id']) ? 'Belum Dihuni' : 'Sudah Dihuni'), 0)
                                    ->getStyle('M'.$nomor)->applyFromArray($styleBorder);
                        $spreadsheet->getActiveSheet()->setCellValue('N'.$nomor, ($r['status_tagihan'] ? 'Terbayar' : 'Belum Terbayar'), 0)
                                    ->getStyle('N'.$nomor)->applyFromArray($styleBorder);
                        $nomor++;
                    // }
                }
            // }
        }
        else
        {
            $spreadsheet->getActiveSheet()->setCellValue('A'.$nomor, "No data available in table")->mergeCells("A".$nomor.":N".$nomor)->getStyle("A".$nomor.":N".$nomor)->applyFromArray($styleBorder)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $nomor++;
        }
        $spreadsheet->getActiveSheet()->getStyle('F1:F'.$spreadsheet->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
        $spreadsheet->getActiveSheet()->getStyle('G1:G'.$spreadsheet->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function get_range_date()
    {
        $start_date = $this->input->post('start_date');
        $end_date   = date('Y-m-d', strtotime($start_date . '+7 days'));
        echo json_encode([
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
    }
}