/*
 Template Name: Veltrix - Responsive Bootstrap 4 Admin Dashboard
 Author: Themesbrand
 File: Datatable js
 */

$(document).ready(function() {
    $('#datatable').DataTable({
        language: {
    	    "url": "public/assets/pages/datatable.localisation_fr.json"
    	},
    });

    //Buttons examples
    var table = $('#datatable-buttons').DataTable({
    	select: true,
    	dom: 'Bfrtip',
        buttons: ['copy', 'excel', 'pdf', 'colvis'],
        "order": false,
        //"scrollY": 200,
        //"scrollX": true,
        "lengthMenu": [[5, 25, 50, -1], [5, 25, 50, "All"]],
        language: {
    	    "url": "public/assets/pages/datatable.localisation_fr.json"
    	}
    });

    table.buttons().container()
        .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
} );