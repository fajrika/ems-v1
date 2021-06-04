
<link href="<?= base_url('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css'); ?>" rel="stylesheet">
<script type="text/javascript" src="<?= base_url('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js'); ?>"></script>
<form action="<?=site_url('Transaksi/Maintenance-meter-air/add-modal/'.$unit->id);?>" id="form-meterair" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post" autocomplete="off">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top:20px">
        <div class="form-group">
            <label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Kawasan - Blok - Unit - Pemilik</label>
            <div class="col-lg-8 col-md-9 col-sm-12 col-xs-12">
                <select name="unit" required="" id="unit" class="form-control" placeholder="-- Pilih Kawasan - Blok - Unit - Pemilik --" readonly>
                    <?php if ($unit->id != 0): ?>
                        <option selected value="<?= $unit->id ?>"><?= $unit->text ?></option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12" style="margin-top:20px">
        <div class="form-group">
            <label class="control-label col-lg-4 col-md-4 col-sm-12 col-xs-12">Jenis Transaksi <span>*</span></label>
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                <select name="jenis_transaksi" id="jenis_transaksi" class="form-control select2" style="width: 100%">
                    <option value="">Pilih Transaksi</option>
                    <?php if($aktif_baru->num_rows()>0): ?>
                    <?php else: ?>
                        <option value="1">Pengaktifan Baru</option>
                        <option value="2">Pengaktifan Kembali</option>
                    <?php endif; ?>
                    <option value="3">Rusak</option>
                    <option value="4">Pemutusan / Tidak Aktif</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-4 col-md-4 col-sm-12 col-xs-12">No. Seri Meter</label>
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                <select name="m_meter_air_id" id="m_meter_air_id" class="form-control select2" style="width: 100%;">
                    <option value="">Pilih No. Seri Meter</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-4 col-md-4 col-sm-12 col-xs-12" id="label_biaya_admin">ID Barcode Meter</label>
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                <input type="text" id="barcode_meter" name="barcode_meter" class="form-control" readonly>
            </div>
        </div>

        <div class="form-group">
            <div id="display-tgl-pasang">
                <label class="control-label col-lg-4 col-md-4 col-sm-12 col-xs-12">Tanggal Pasang <span>*</span></label>
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <input type="text" id="tgl_pasang" name="tgl_pasang" class="form-control" maxlength="10" value="<?=date('Y-m-d')?>" placeholder="Tanggal Pasang">
                </div>
            </div>
            <div id="display-tgl-aktif">
                <label class="control-label col-lg-2 col-md-2 col-sm-12 col-xs-12">Tanggal Aktif</label>
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <input type="text" id="tgl_aktif" name="tgl_aktif" class="form-control" maxlength="10" value="<?=date('Y-m-d')?>" placeholder="Tanggal Aktif">
                </div>
            </div>
        </div>

        <div id="display-tgl-pemutusan" style="display: none;">
            <div class="form-group">
                <label class="control-label col-lg-4 col-md-4 col-sm-12 col-xs-12">Tanggal Pemutusan <span>*</span></label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <input type="text" id="tgl_pemutusan" name="tgl_pemutusan" class="form-control" maxlength="10" value="<?=date('Y-m-d')?>" placeholder="Tanggal Pemutusan">
                </div>
            </div>
        </div>

        <div id="display-sub-golongan">
            <div class="form-group">
                <label class="control-label col-lg-4 col-md-4 col-sm-12 col-xs-12">Sub Golongan <span>*</span></label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <select name="sub_gol_id" id="sub_gol_id" class="form-control select2" style="width: 100%;">
                        <option value="">Pilih Sub Golongan</option>
                        <?php if($sub_gol->num_rows() > 0): ?>
                            <?php 
                            foreach($sub_gol->result() as $p):
                                $selected = '';
                                if($p->id == $unit->sub_gol_id){
                                    $selected = 'selected';
                                }
                                ?>
                                <option value="<?=$p->id;?>" <?=$selected;?>><?=$p->name;?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <div id="display-biaya-sambungan">
            <div class="form-group">
                <label class="control-label col-lg-4 col-md-4 col-sm-12 col-xs-12">Biaya Sambungan <span>*</span></label>
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <?php
                    $q = $this->db
                        ->select('ISNULL(pemeliharaan_air.nilai_pemasangan + nilai_ppn_pemasangan, 0) AS biaya_sambungan')
                        ->join('pemeliharaan_air', 'sub_golongan.pemeliharaan_air_id = pemeliharaan_air.id')
                        ->where('sub_golongan.id', $unit->sub_gol_id)
                        ->get('sub_golongan');
                    if($q->num_rows() > 0){
                        $biaya = $q->row()->biaya_sambungan;
                    }else{
                        $biaya = '0';
                    }
                    ?>
                    <input type="text" name="nilai_penyambungan" id="nilai_penyambungan" class="form-control" placeholder="Biaya Sambungan" readonly="" value="<?=@$biaya;?>">
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12" style="margin-top:20px">
        <div id="display-biaya-admin">
            <div class="form-group">
                <label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Biaya Admin</label>
                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                    <input type="text" name="biaya_admin" id="biaya_admin" class="form-control" placeholder="Biaya Admin">
                </div>
            </div>
        </div>

        <div id="display-total-biaya">
            <div class="form-group">
                <label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Total Biaya <span>*</span></label>
                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                    <input type="text" name="total_biaya" id="total_biaya" class="form-control" placeholder="Total Biaya" readonly="">
                </div>
            </div>
        </div>

        <div class="form-group">
            <div id="display-meter-awal">
                <label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Meter Awal</label>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <input type="text" id="meter_awal" name="meter_awal" class="form-control" maxlength="10" placeholder="Meter Awal">
                </div>
            </div>
            <div id="display-meter-akhir">
                <label class="control-label col-lg-2 col-md-2 col-sm-12 col-xs-12" style="padding-left: 0;">Meter Akhir</label>
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="padding-left: 0;">
                    <input type="text" id="meter_akhir" name="meter_akhir" class="form-control" maxlength="10" placeholder="Meter Akhir" readonly="">
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Petugas</label>
            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                <input type="text" name="petugas" class="form-control" placeholder="Petugas">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Keterangan</label>
            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                <textarea class="form-control" name="keterangan" rows="3" placeholder="...." style="resize: vertical;"></textarea>
            </div>
        </div>
        <div id="display-dokumen">
            <div class="form-group">
                <label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Dokumen</label>
                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                    <input type="file" name="dokumen" id="dokumen" class="form-control" data-container="body" data-toggle="popover" data-placement="top" data-content="Allowed Size : 5MB per file" data-trigger="hover">
                </div>
            </div>
        </div>
        <input type="hidden" name="tipe_transaksi" id="tipe_transaksi" value="aktif_baru">
        <input type="hidden" name="air_unit_id" id="air_unit_id" value="<?= $unit->id ?>">
    </div>
</form>

<div style="padding-left: 25px; padding-right: 25px; padding-top: 30px;">
    <div class="card-box table-responsive">
        <table class="table table-striped jambo_table" id="table-meter-air" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Transaksi</th>
                    <th>Sub Golongan</th>
                    <th>No. Seri Meter</th>
                    <th>Tgl. Pasang</th>
                    <th>Tgl. Aktif</th>
                    <th class="no-sort">Tgl. Pemutusan</th>
                    <th class="no-sort">Biaya Sambungan</th>
                    <th class="no-sort">Biaya Admin</th>
                    <th class="no-sort">Meter Awal</th>
                    <th class="no-sort">Meter Akhir</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#jenis_transaksi, #m_meter_air_id, #sub_gol_id').select2();
        $('[data-toggle="popover"]').popover();
        reset = '<button type="reset" id="btn-reset" class="btn btn-sm btn-primary"><i class="fa fa-refresh"></i> Reset</button>';
        <?php if (permission() ? permission()->create : 0) : ?>
            submit = '<button type="button" class="btn btn-sm btn-success" id="save_maintenance_air"><i class="fa fa-edit"></i> Submit</button>';
        <?php else: ?>
            submit = '';
        <?php endif; ?>
        close = '<button type="button" class="btn btn-sm btn-info" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>';
        $('#footer-maintenance-air').html(reset + submit + close);

        $("#tgl_pasang, #tgl_aktif, #tgl_pemutusan").datetimepicker({
            viewMode: 'days',
            format: 'YYYY-MM-DD'
        });

        $("#biaya_admin").keyup(function() {
            nilai_penyambungan = unformatNumber($('#nilai_penyambungan').val())=='' ? 0 : unformatNumber($('#nilai_penyambungan').val());
            biaya_admin = this.value=='' ? 0 : this.value;
            total_biaya = parseFloat(nilai_penyambungan) + parseFloat(biaya_admin);

            $('#total_biaya').val(formatOnlyNumber(total_biaya));
        });
        $("#meter_awal,#meter_akhir").keyup(function() {
            $(this).val(formatOnlyNumber($(this).val()));
        });

        $('#btn-reset').on('click', function(e){
            e.preventDefault();
            $("#form-meterair")[0].reset();
        });

        // function show table
        meterstable = $('#table-meter-air').DataTable({
            "serverSide": true,
            "stateSave": false,
            "bAutoWidth": true,
            "bDestroy" : true,
            "oLanguage": {
                "sSearch": "Search : ",
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
                "sInfoFiltered": "(filtered from _MAX_ total entries)",
                "sZeroRecords": "<center>Data tidak ditemukan</center>",
                "sEmptyTable": "No data available in table",
                "sLoadingRecords": "Please wait - loading...",
                "oPaginate": {
                    "sPrevious": "Prev",
                    "sNext": "Next"
                }
            },
            "aaSorting": [
                [0, "asc"]
            ],
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false
            }],
            "sPaginationType": "simple_numbers",
            "iDisplayLength": 10,
            "aLengthMenu": [
                [10, 20, 50, 100, 150, 200], [10, 20, 50, 100, 150, 200]
            ],
            "ajax": {
                url: "<?= site_url('transaksi/maintenance-meter-air/request-data-json'); ?>",
                type: "get",
                cache: false,
                data: {unit_id: $("#unit").val().split('.')[1]}
            }   
        });
    });

    $(document).on('change', '#sub_gol_id', function(e){
        e.preventDefault();
        total_biaya = 0;
        biaya_admin = $('#biaya_admin').val()==''?0:$('#biaya_admin').val();

        $.ajax({
            url: "<?=site_url('transaksi/maintenance-meter-air/biaya-sambungan');?>",
            cache: false,
            type: 'POST',
            data: {id: this.value},
            dataType: 'json',
            success: function(data) {
                $('#nilai_penyambungan').val(formatOnlyNumber(data.biaya_sambungan));
                total_biaya = parseFloat(data.biaya_sambungan) + parseFloat(biaya_admin);
                $('#total_biaya').val(formatOnlyNumber(total_biaya));
            }
        });
    });

    function unformatNumber(data) {
        data = data + '';
        return data.replace(/,/g, "");
    }

    function formatOnlyNumber(data) {
        data = data + '';
        data = data.replace(/,/g, "");
        data = parseInt(data) ? parseInt(data) : 0;
        data = data.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
        return data;
    }
</script>
<style>
    #form-meterair input::placeholder { font-size: 11px; }
    #form-meterair input { font-size: 12px; }
    #form-meterair .select2-container--default,
    #form-meterair .select2-selection--single { 
        min-height: 34px !important; 
    }
    #form-meterair .select2-container--default, 
    #form-meterair .select2-selection--single,
    #form-meterair .select2-selection__rendered {
        line-height: 22px;
        font-size: 12px;
    }

    #table-meter-air{ font-size: 12px; }
    #table-meter-air thead th{ border: 1px solid #556c81 !important; }
    div.dataTables_wrapper { width: 99%; height: 100%; margin: 0 auto; }
    #table-meter-air>thead>tr>th { border-bottom: 1px solid #556c81 !important; padding: 10px 9px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    #table-meter-air>tbody>tr>td { border: 1px solid #ddd; }
    .dataTables_length select,
    .dataTables_filter input { padding: 6px 12px; border: 1px solid #ccc; border-radius: 0px; }
    tfoot input::placeholder { font-size:11px; }
    #table-meter-air tbody { font-size: 13px; }
    #table-meter-air.dataTables_length { display: block !important; }
    #table-meter-air_wrapper { height: auto !important; }
</style>