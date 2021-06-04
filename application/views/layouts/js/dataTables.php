<!-- Datatables -->
<script src="<?= base_url("assets/gentelella/datatables.net/js/jquery.dataTables.min.js") ?>"></script>
<script src="<?= base_url("assets/gentelella/datatables.net-bs/js/dataTables.bootstrap.min.js") ?>"></script>
<script src="<?= base_url("assets/gentelella/datatables.net-buttons/js/dataTables.buttons.min.js") ?>"></script>
<script src="<?= base_url("assets/gentelella/datatables.net-buttons-bs/js/buttons.bootstrap.min.js") ?>"></script>
<script src="<?= base_url("assets/gentelella/datatables.net-buttons/js/buttons.flash.min.js") ?>"></script>
<script src="<?= base_url("assets/gentelella/datatables.net-buttons/js/buttons.html5.min.js") ?>"></script>
<script src="<?= base_url("assets/gentelella/datatables.net-buttons/js/buttons.print.min.js") ?>"></script>
<script src="<?= base_url("assets/gentelella/datatables.net-buttons/js/buttons.colVis.min.js") ?>"></script>
<script src="<?= base_url("assets/gentelella/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js") ?>"></script>
<script src="<?= base_url("assets/gentelella/datatables.net-keytable/js/dataTables.keyTable.min.js") ?>"></script>
<!-- <script src="<?= base_url("assets/gentelella/datatables.net-responsive/js/dataTables.responsive.min.js") ?>"></script> -->
<!-- <script src="<?= base_url("assets/gentelella/datatables.net-responsive-bs/js/responsive.bootstrap.js") ?>"></script> -->
<script src="<?= base_url("assets/gentelella/datatables.net-scroller/js/dataTables.scroller.min.js") ?>"></script>
<script src="<?= base_url("assets/gentelella/jszip/dist/jszip.min.js") ?>"></script>
<script src="<?= base_url("assets/gentelella/pdfmake/build/pdfmake.min.js") ?>"></script>
<script src="<?= base_url("assets/gentelella/pdfmake/build/vfs_fonts.js") ?>"></script>


<script>
    $(document).ready(function() {
        // Setup - add a text input to each footer cell
        $('#datatable-responsive tfoot th').each(function() {
            var title = $(this).text();
            $(this).html('<input type="text" placeholder="Search ' + title + '" />');
        });

        // DataTable
        var table = $('#datatable-responsive').DataTable({
            // "columnDefs": [{
            //     // "searchable": false,
            //     "orderable": false,
            //     "targets": 0
            // }],
            "order": [
                [1, 'asc']
            ],
            initComplete: function() {
                // Apply the search
                this.api().columns().every(function() {
                    var that = this;
                    $('input', this.footer()).on('keyup change clear', function() {
                        if (that.search() !== this.value) {
                            that
                                .search(this.value)
                                .draw();
                        }
                    });
                });
            },
            "processing": true,
            // "serverSide": true,
            // ajax: {
            //     url: "<?= base_url("Projects/APIs/Masters/Accountings/PT") ?>",
            //     type: "GET"
            // },
            // sDom: 'lrtip',
            dom: '<"top"B>rti',
            buttons: [{
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: [0, ':visible']
                    },
                    className: 'btn btn-success'
                }, {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':visible:not(:eq(0))'
                    },
                    className: 'btn btn-success'
                }, {
                    extend: 'csvHtml5',
                    exportOptions: {
                        columns: ':visible:not(:eq(0))'
                    },
                    className: 'btn btn-success'
                }, {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: ':visible:not(:eq(0))'
                    },
                    className: 'btn btn-success'
                },
                {
                    extend: 'colvis',
                    className: 'btn btn-success'
                }
                // {
                //     text: 'Add',
                //     className: 'btn btn-success disabled'
                // }
            ],
            "lengthMenu": [
                [-1, 25, 50, -1],
                ["All", 25, 50, "All"]
            ]

        });
        // merubah id jadi row number fix
        // table.on('draw.dt', function() {
        //     table.column(0, {
        //         search: 'applied',
        //         order: 'applied'
        //     }).nodes().each(function(cell, i) {
        //         cell.innerHTML = i + 1;
        //     });
        // }).draw();
    });
</script>