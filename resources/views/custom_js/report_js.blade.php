<script>
    $(document).ready(function () {

        $('#example').DataTable({

    "bFilter": true,
    'pagingType': 'numbers',
    "ordering": true,
    "language": {
        search: ' ',
        sLengthMenu: '_MENU_',
        searchPlaceholder: 'البحث',
        info: "_START_ - _END_ من _TOTAL_ عناصر",
    },
    dom: 'Blfrtip',
    buttons: [
        {
            extend: 'print',
            text: 'طباعة', // Arabic for "Print"
            footer: true,
            title: '',
            filename: 'Report',
            customize: function (win) {

                $(win.document.body).prepend(`
                    <div style="text-align:center;  margin-top:10px;"><h3><?php echo "$about->about_name"; ?></h3></div>
                    <div style="border:1px solid #333; display: flex; justify-content: space-between; padding: 5px; margin-top:10px;">
                        <div>date from: <?php echo "$sdate"; ?>  </div> <div>to date: <?php echo "$edate"; ?>  </div>
                        <div><?php echo $report_name; ?></div>
                    </div>`);
                }
        },
        {
            extend: 'csv',
            text: 'تصدير CSV', // Arabic for "Export CSV"
            footer: true,
            title: '',
            filename: 'Report'
        },
    ],
    initComplete: (settings, json) => {
        $('.dataTables_filter').appendTo('#tableSearch');
        $('.dataTables_filter').appendTo('.search-input');
    },
});


        var bank_statement = $('#bank_statement').DataTable({
            "pageLength": 500,
            dom: 'Blfript',
            buttons: [
                {
                    extend: 'print',
                    footer: true,
                    title: '',
                    visible: false,
                    filename: 'Report',
                    customize: function (win) {
                        $(win.document.body).prepend(`

                            <div style="text-align:center; margin-top:10px;">
                                <h3><?php echo "$about->about_name"; ?></h3>
                            </div>
                            <div style="border:1px solid #333; display: flex; justify-content: space-between; padding: 5px; margin-top:10px;">
                                <div>date from: <?php echo "$sdate"; ?></div>
                                <div>to date: <?php echo "$edate"; ?></div>
                                <div><?php echo $report_name; ?></div>
                            </div>
                        `);
                    }
                },
                {
                    extend: 'csv',
                    footer: true,
                    title: '',
                    filename: 'Report',
                    visible: false
                },
            ],
            "order": [[1, 'asc']], // Sort by the second column (date) in ascending order
            "columnDefs": [
                {
                    "type": "datetime-moment",
                    "targets": 1 // Target the second column for date type sorting
                }
            ],
            "fnInitComplete": function(oSettings, json) {
                show_balance();
            },
        });
    });




function show_balance()
{
    var counter=0;
    var bot=0;
    var bin=0;
    var bbl=0;
    $('.bank-in').each(function(e){

        if(counter==0)
        {
            bbl =$('.bank-balance').text();
        }
        else
        {
             bbl =$(this).closest('tr').prev().find('.bank-balance').text();
        }
        bin =$(this).text();
        bot =$(this).closest('tr').find('.bank-out').text();
        if(bin==''){
           bin=0;
         }
         if(bot==''){
           bot=0;
         }

        var balance =parseFloat(bbl)+parseFloat(bin);

        var final_balance=balance -  bot  ;
        $(this).closest('tr').find('.bank-balance').text(final_balance.toFixed(3));
        counter++;
    });
}


    // order detail data
function formatDate(date) {
    var dd = date.getDate();
    var mm = date.getMonth() + 1; // January is 0!
    var yyyy = date.getFullYear();

    if (dd < 10) {
        dd = '0' + dd;
    }

    if (mm < 10) {
        mm = '0' + mm;
    }

    return yyyy + '-' + mm + '-' + dd;
}



function setDates(period) {
        // Get the current date
        var currentDate = new Date();
        var dateFrom = new Date();

        // Adjust the "from" date based on the selected period
        switch (period) {
            case 'week':
                dateFrom.setDate(currentDate.getDate() - 7); // One week ago
                break;
            case 'month':
                dateFrom.setMonth(currentDate.getMonth() - 1); // One month ago
                break;
            case '3months':
                dateFrom.setMonth(currentDate.getMonth() - 3); // Three months ago
                break;
            case '6months':
                dateFrom.setMonth(currentDate.getMonth() - 6); // Six months ago
                break;
            case 'year':
                dateFrom.setFullYear(currentDate.getFullYear() - 1); // One year ago
                break;
        }

        // Format dates as 'YYYY-MM-DD' (for input fields)
        var dateFromFormatted = dateFrom.toISOString().split('T')[0];
        var currentDateFormatted = currentDate.toISOString().split('T')[0];

        // Set the values to the form inputs
        document.getElementById('date_from').value = dateFromFormatted;
        document.getElementById('to_date').value = currentDateFormatted;

        // Automatically submit the form

    }


</script>
