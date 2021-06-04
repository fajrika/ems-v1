<div id="modal-progress" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-justify">Process Migration</h5>
                <div style="display: inline;">
                    <p>Waktu Mulai : <span id="waktu_mulai_migration" style="float: right;"> </span></p>
                    <p>Timer : <span id="timer" style="float: right;"></span></p>
                </div>
            </div>
            <div class="modal-body">
                <div class="progress">
                    <div id="pb-length" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                    <div id="pb-label" class="progress-bar-striped ui-progressbar ui-corner-all ui-widget ui-widget-content" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 100%;text-align: center;position: absolute;"></div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 form-group d-flex justify-content-center">
                    <label class="control-label col-lg-5 col-md-5 col-sm-5 justify-content-center align-self-center">Data yang telah Termigrasi Sebelumnya</label>
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <input id="data-migrasi1" type="text" class="form-control" disabled>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 form-group d-flex justify-content-center">
                    <label class="control-label col-lg-5 col-md-5 col-sm-5 justify-content-center align-self-center">Data yang telah Termigrasi (saat ini)</label>
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <input id="data-migrasi2" type="text" class="form-control" disabled>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 form-group d-flex justify-content-center">
                    <label class="control-label col-lg-5 col-md-5 col-sm-5 justify-content-center align-self-center">Data yang telah Termigrasi (akumulasi)</label>
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <input id="data-migrasi3" type="text" class="form-control" disabled>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 form-group d-flex justify-content-center mt-5">
                    <label class="control-label col-lg-5 col-md-5 col-sm-5 justify-content-center align-self-center">Data yang belum Termigrasi Sebelumnya</label>
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <input id="data-migrasi4" type="text" class="form-control" disabled>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 form-group d-flex justify-content-center">
                    <label class="control-label col-lg-5 col-md-5 col-sm-5 justify-content-center align-self-center">Data yang belum Termigrasi (saat ini)</label>
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <input id="data-migrasi5" type="text" class="form-control" disabled>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 form-group d-flex justify-content-center mt-5">
                    <label class="control-label col-lg-5 col-md-5 col-sm-5 justify-content-center align-self-center">Proses</label>
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <input id="data-migrasi6" type="text" class="form-control" disabled>
                    </div>
                </div>
            </div>
            <div id="modal-footer" class="modal-footer col-md-12" style="justify-content: flex-start">
                <!-- <h6 class="col-lg-8 col-md-8 col-sm-8 justify-content-center align-self-center" style="color: red;">Error, koneksi Terputus silahkan di reload</h6>
                <button class="btn btn-success">Reload</button> -->
                <button id="modal-btn-close" type="button" class="btn btn-secondary offset-md-10" data-dismiss="modal" disabled="">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel ">
        <div class="x_title">
            <h2>Info<small>Unit</small></h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <br>
            <form id="formSync" data-parsley-validate="" class="form-horizontal form-label-left" method="post" action="<?= site_url('Sync/Desktop_tagihan_air/save2') ?>" autocomplete="off">
                <div class="col-lg-12 col-md-12 col-sm-12 form-group d-flex justify-content-center">
                    <label class="control-label col-lg-1 col-md-3 col-sm-3">Source</label>
                    <div class="col-lg-5 col-md-5">
                        <select id="source" type="text" class="select2 form-control col-md-12" name="source" required>
                            <option></option>
                            <?php foreach ($schemas as $schema) : ?>
                                <option value="<?= $schema->name ?>"><?= $schema->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 form-group d-flex justify-content-center">
                    <label class="control-label col-lg-1 col-md-3 col-sm-3">Jenis Denda</label>
                    <div class="col-lg-5 col-md-5">
                        <select id="denda_jenis_service" type="text" class="select2 form-control" name="denda_jenis_service" placeholder="Project" required>
                            <option></option>
                            <option value="1">Fixed</option>
                            <option value="2">Progresif</option>
                            <option value="3">Persen Progresif</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 form-group d-flex justify-content-center">
                    <label class="control-label col-lg-1 col-md-3 col-sm-3">Nilai Denda</label>
                    <div class="col-lg-5 col-md-5">
                        <input id="denda_nilai_service" type="text" class="form-control" name="denda_nilai_service" placeholder="Masukkan Nilai Denda Default" required>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 form-group d-flex justify-content-center">
                    <label class="control-label col-lg-1 col-md-3 col-sm-3">Jarak Periode Pemakaian dengan Periode Tagihan</label>
                    <div class="col-lg-5 col-md-5">
                        <input id="jarak_periode" type="text" class="form-control" name="jarak_periode" placeholder="Masukkan Jarak" required>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 form-group d-flex justify-content-center">
                    <label class="control-label col-lg-1 col-md-3 col-sm-3">Ke Project</label>
                    <div class="col-lg-5 col-md-5">
                        <select id="project_id" type="text" class="select2 form-control" name="project_id" placeholder="Project" required>
                            <option></option>
                            <?php foreach ($project as $v) : ?>
                                <option value="<?= $v->id ?>"><?= $v->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 form-group d-flex justify-content-center">
                    <div class="form-group" style="margin-top:20px">
                        <div class="center-margin">
                            <!-- <button class="btn btn-primary" type="reset" style="margin-left: 110px">Reset</button> -->
                            <button id="btn-submit-formSync" type="submit" class="btn btn-success">Get Data</button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="clearfix"></div>
            <br>
            <br>
            <div class="table-responsive">

            </div>
            <!-- </form> -->
            <br>
        </div>
    </div>
</div>