<!DOCTYPE html>
<html>
<head>
    <title>Query Logs</title>
	<!-- jQuery and jQuery UI -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<!-- Timepicker Addon -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.jqueryui.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.jqueryui.min.js"></script>
	<script>
	$(function () {
    $('#filter_from').datetimepicker({
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm'
    });
    $('#filter_to').datetimepicker({
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm'
    });
});
	</script>
</head>
<body>

<h2>Query Logs Viewer</h2>
<style>
  
#toolbarContainer{display:none!important;}
#logTable {
    font-size: 14px;
}
.query-cell-right{text-align:right;}

.query-cell {
    max-width: 600px;
    width:300px;
    white-space: pre-wrap;
    word-break: break-word;
    font-family: monospace;
    background: #f8f9fa;
    padding: 8px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.query-cell.collapsed {
    max-height: 80px;
    overflow: hidden;
    position: relative;
}
.query-cell.collapsed::after {
    content: '▼';
    position: absolute;
    bottom: 5px;
    right: 10px;
    background: #fff;
    padding-left: 5px;
}
.query-cell.expanded::after {
    content: '▲';
}

    </style>
<div>
    <label>Company:</label>
    <select id="filter_company" class="form_control">
        <option value="">Choose</option>
		<?php if($companies){ foreach($companies as $company_id => $comany_name){?>
		<option value="<?php echo $company_id;?>"><?php echo strtoupper($comany_name);?></option>
		<?php } }  ?>
        <!-- Add more options dynamically if needed -->
    </select>

    <label>From:</label>
    <input type="datetime-loca" id="filter_from">

    <label>To:</label>
    <input type="datetime-loca" id="filter_to">
	<label>Size:</label>
	<select id="filter_size_condition">
		<option value=""></option>
		<option value=">">></option>
		<option value="<"><</option>
		<option value="=">=</option>
	</select>
   <input type="number" id="filter_size_value" placeholder="ms" min="0" step="1" style="width:80px;">
	<input type="button" class="button" id="gofilters" value="Go">
	&nbsp;&nbsp;
	<input type="button" class="button" id="delrcds" value="Delete All Records">
</div>

<br>
<table id="logTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th>#</th>
            <th>Page</th>
            <th>Query</th>
            <th>Time (ms)</th>
            <th>Entry At</th>
        </tr>
    </thead>
</table>

<script>
let table;
$(document).on('click', '.query-cell', function () {
    $(this).toggleClass('expanded');
});

$(document).on('click', '#delrcds', function () {
	$('#filter_company').val('');
                $('#filter_from').val('');
                $('#filter_to').val('');
                $('#filter_size_condition').val('');
                $('#filter_size_value').val('');
   $.ajax({
            url: '<?= base_url("LogViewer/delete_all") ?>', // adjust this to your route
            method: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.status === true) {
                    table.clear().draw(); // Clear table view
                    alert('All records deleted successfully.');
                } else {
                    alert('Failed to delete records.');
                }
            },
            error: function () {
                alert('AJAX error: Could not delete records.');
            }
        });
});




function loadTable() {
    table = $('#logTable').DataTable({
        destroy: true,
        processing: true,
        serverSide: false,
        searching: true,
        paging: true,
        ordering: false,
        ajax: {
            url: "<?= base_url('LogViewer/ajax') ?>",
            type: "POST",
            data: function (d) {
                d.company = $('#filter_company').val();
                d.from = $('#filter_from').val();
                d.to = $('#filter_to').val();
				d.size_operator = $('#filter_size_condition').val();
			    d.size_value = $('#filter_size_value').val();
            }
        },
        order: [[4, "desc"]],
        columns: [
        { data: 'id' },
        { data: 'uri' },
        {
            data: 'sql_stmt',
            className: 'query-cell'
        },
        { data: 'elapsed_ms'},
        { data: 'created_at'}
    ]
    });
	
	
}

// Initial state — do not load table
$(document).ready(function () {
	
    $('#logTable').DataTable({
        destroy: true,
        data: [],
        columns: [
            { title: "#" },
            { title: "Page" },
           {
            data: 'queries_log',
            render: function (data, type, row) {
                return `<div class="query-cell collapsed">${data}</div>`;
            }
        },
            { title: "Time (ms)" },
            { title: "Logged At" }
        ]
    });

    // When any filter changes, load data if filters are selected
    $('#gofilters').on('click', function () {
        const comp = $('#filter_company').val();
        const from = $('#filter_from').val();
        const to = $('#filter_to').val();

        if (comp!='' && from!='' && to!='') {
            loadTable();

            // Start auto-refresh every 5 sec
            
            /* if (!window.logAutoRefresher) {
                window.logAutoRefresher = setInterval(function () {
                    table.ajax.reload(null, false);
                }, 10000);
            } */
        }
    });
});
</script>

</body>
</html>
