// v2 1st attempt got deleted
// literally the only folder I hadn't got
// snapshots running on; I hate life!

$( document ).ready(function() {

	// Grab Total Investments from API
        function updateStats() {

		$.ajax({

			url: "?api=totals",
			dataType: "json",
			cache: false,
			success: function(data) {

				$('#totalInvested').html(data.invested);
				$('#totalValue').html(data.value);

				$panelClass = 'panel-warning'
				switch(data.diff) {

					case "Less":
					  $panelClass = 'panel-danger';
					  break;

					case "MoreOrEqual":
					  $panelClass = 'panel-success';
					  break;

				}

				// If further panel classes are added to HTML, then will
				// have to use an alternate strategy that removes all classes
				// and rewrites them according to condition.
				$('#valueStatBox').attr('class', $('#valueStatBox').get(0).className.replace(/\bpanel-\S+/g, $panelClass));

			}
            
		});
        
        }

	// Update Stat Boxes
        updateStats();

	// Configure & Populate Portfolio Table
	$portfolioTable = $('#portfolio').DataTable( {
		ajax: {
			url: '?api=portfolio',
			dataSrc: ''
		},

		buttons: [
				{
					text: 'Reload Market Data',
					action: function ( e, dt, node, config ) {

						updateStats();
						dt.ajax.reload();

					}
				}
		],

		columns: [

			{ data: 'id' },
			{ data: 'name' },
			{ data: 'symbol' },
			{ data: 'marketCap' },
			{ data: 'invested' },
			{ data: 'amount' },
			{ data: 'value' },
			{ data: 'change' }

		],

		"order": [[ 7, "desc" ]],

		"columnDefs": [
			{
				"targets": [ 0 ],
				"visible": false,
				"searchable": false
			}
		],

		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			
			$(nRow).css('cursor', 'pointer');
			$(nRow).attr('onclick', "window.open('https://coinmarketcap.com/currencies/" + aData['id'] + "/')");

			if ( aData['change'].includes("-") )
			{
				$(nRow).addClass('danger');
			}
			else
			{
				$(nRow).addClass('success');
			}

		},

		dom: 'lBfrtip',

	} );

	// Poll API for New Data Every 2 1/2 Minutes
	setInterval( function () {

		updateStats();
		$portfolioTable.ajax.reload();

	}, 150000 );

});
