// A $( document ).ready() block.
$( document ).ready(function() {
 //   $('.custom-scroll').slimscroll({	  
	//   height: 'auto',	  
 //      color: '#000',      
 //      opacity: 0.2
	// });
	// Initialize the plugin
    const demo = document.querySelector('.custom-scroll');
    const ps = new PerfectScrollbar(demo);

	
	oTable = $('#table_id').DataTable({
	 	paging: true,
	 	searching: true,
	 	autoWidth: false,	
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true,
	 	language: {
	        search: "_INPUT_",
	        searchPlaceholder: "search by name, lacation, account and number",
	        "paginate": {
		      "previous": "<span class='icon-moon icon-Drop-Down-1'></span>",
		      "next": "<span class='icon-moon icon-Drop-Down-1'></span>"
		    }
	    },
	 	"dom": '<"custom-table-header"lf<"refresh">><"custom-table-body"rt><"custom-table-footer"ip>'	 	
	 	

	});		

	$('#myInputTextField').keypress(function(){
	    console.log('pressed');
	    oTable.search($(this).val()).draw();
	})
	
	$('.btn-profile-toggle').click(function () {
		$('.profile-dropdown ul').toggleClass('open');
	})
	$('#toggle_sidebar').click(function () {
		$('.sidebar-area').toggleClass('sidebar-collapsed');
		$('body').toggleClass('sidebar-collapsed');
	})	
	$('.open-submenu').click(function () {
		$(this).toggleClass('open');
		$(this).parent().next().toggleClass('open');
	})
});
