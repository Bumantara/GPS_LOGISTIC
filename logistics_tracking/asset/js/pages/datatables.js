$(function(){
	$("#datatables-1").dataTable();
    $("#datatables-10").dataTable();
    $("#datatables-11").dataTable();
    $("#datatables-12").dataTable();
    $("#datatables-13").dataTable();
    $("#datatables-14").dataTable();
    $("#datatables-15").dataTable();
    $("#datatables-16").dataTable();
    $("#datatables-17").dataTable();
    $("#datatables-18").dataTable();
    $("#datatables-19").dataTable();
    $("#datatables-20").dataTable();
    $("#datatables-21").dataTable();
    $("#datatables-22").dataTable();
    $("#datatables-23").dataTable();
    $("#datatables-24").dataTable();
    $("#datatables-25").dataTable();
    $("#datatables-26").dataTable();
    $("#datatables-27").dataTable();
    $("#datatables-28").dataTable();
    $("#datatables-29").dataTable();
    $("#datatables-30").dataTable();

	
    $('#datatables-3').dataTable( {
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            data = api.column( 4 ).data();
            total = data.length ?
                data.reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                } ) :
                0;
 
            // Total over this page
            data = api.column( 4, { page: 'current'} ).data();
            pageTotal = data.length ?
                data.reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                } ) :
                0;
 
            // Update footer
            $( api.column( 4 ).footer() ).html(
                '$'+pageTotal +' ( $'+ total +' total)'
            );
        }
    } );
    $('#datatables-4').DataTable( {
        dom: 'T<"clear">lfrtip',
        tableTools: {
            "sSwfPath": "./assets/libs/jquery-datatables/extensions/TableTools/swf/copy_csv_xls_pdf.swf"
        }
    } );    
})