var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
}

$(document).ready( function () {

	$('#login-form').submit(function(event) {
		if ($('#stuID').val() == "" || $('#pwd').val() == "") {
			event.preventDefault();
		}
	})
	$('#reserve-form').submit(function(event) {
		var selectedPeriods = new Array();
		$("input:checkbox[name=period]:checked").each(function() {
			selectedPeriods.push($(this).val());
		})
		$('#periods').val(selectedPeriods);

		if ($('#stuName').val() == "" || $('#room').val() == "" || $('#date').val() == "" || $('#room').val() == "" || selectedPeriods.length == 0 || $('#purpose').val() == "") {
			event.preventDefault();
			$('#info-msg').show();
		}
	})

	$('[data-toggle="tooltip"]').tooltip(); 

	$('.fader').mouseover(function() {
		$(this).css({opacity:'1'});
		$(this).animate({opacity: 0.5}, 'fast');
		$(this).html("View");
	});

	$('.fader').mouseout(function() {
		$(this).css({opacity:'0.5'});
		$(this).animate({opacity: 1}, 'fast');
		$(this).html("");
	});

	if($(window).width() <= 1030) {
      $('#mainNavbar').removeClass("in").addClass("collapse");
    } else {
      $('#mainNavbar').removeClass("collapse").addClass("in");
    }

	$( window ).resize(function() {
		if($(window).width() <= 1030) {
	      $('#mainNavbar').removeClass("in").addClass("collapse");
	    } else {
	      $('#mainNavbar').removeClass("collapse").addClass("in");
	    }
	})

	$(function() {
	  $("#displayTable").tablesorter();
	});

    $(function () {
        $('#datepicker').datetimepicker({
        	format:"YYYY-MM-DD",
        	defaultDate:new Date()
        });
    });

	  $("#roomsSearchBar").on("keyup", function() {roomSearch();});
	  $("#roomsFloor").change( function() {roomSearch();});
	  function roomSearch() {
	  	var value = $("#roomsSearchBar").val().toLowerCase();
	    $("#rooms-wrapper > div").filter(function() {
	      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
	    });

	    var filterVal = $("#roomsFloor").val();
	  	switch($("#roomsFloor").val()) {
	  		case "all":
	  			filterVal = "";
	  			break;
	  		case "1":
	  			filterVal += "st";
	  			break;
	  		case "2":
	  			filterVal += "nd";
	  			break;
	  		default:
	  			filterVal += "th";
	  	}
		$("#rooms-wrapper > div:visible ").filter(function() {
	      $(this).toggle($(this).text().toLowerCase().indexOf(filterVal) > -1);
	    });
	  }


	  $("#acntType").change( function() { acntSearch(); });
	  $("#acntSearchBar").on("keyup", function() { acntSearch(); });
	  function acntSearch() {
	  	var filterClass = "";
	  	switch($("#acntType").val()) {
	  		case "All":
	  			filterClass = "";
	  			break;
	  		case "Stu":
	  			filterClass = "warning";
	  			break;
	  		case "Tr":
	  			filterClass = "success";
	  			break;
	  		case "Admins":
	  			filterClass = "info";
	  	}
	  	if (filterClass != "") {
			$("#displayTable > tbody > tr:not(#new_row)").filter(function() {
		      $(this).toggle($(this).hasClass(filterClass));
		    });
	  	} else {
	  		$("#displayTable > tbody > tr:not(#new_row)").show();
	  	}

	  	var value = $("#acntSearchBar").val().toLowerCase();
	  	$("#displayTable > tbody > tr:visible ").filter(function() {
	      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
	    });
	  }


	  $("#historyShow").change( function() { historySearch(); });
	  $("#historyStatus").change( function() { historySearch(); });
	  $("#historySearchBar").on("keyup", function() { historySearch(); });
	  function historySearch() {
	  	var d = new Date();
	  	var month = d.getMonth() + 1;
	  	var day = d.getDate();
	  	var fullDate = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
	  	switch($("#historyShow").val() ) {
	  		case "all":
				$("#displayTable > tbody > tr").show();
	  			break;
	  		case "future":
	  			$("#displayTable > tbody > tr").filter(function() {
			      $(this).toggle($(this).children('th[name="date"]').text() >= fullDate );
			    });
	  			break;
	  		case "past":
	  			$("#displayTable > tbody > tr").filter(function() {
			      $(this).toggle($(this).children('th[name="date"]').text() <= fullDate );
			    });
	  			break;
	  	}

	  	var filterClass = "";
	  	switch($("#historyStatus").val()) {
	  		case "all":
	  			filterClass = "";
	  			break;
	  		case "approved":
	  			filterClass = "success";
	  			break;
	  		case "disapproved":
	  			filterClass = "danger";
	  			break;
	  		case "pending":
	  			filterClass = "warning";
	  	}
	  	if (filterClass != "") {
			$("#displayTable > tbody > tr:visible").filter(function() {
		      $(this).toggle($(this).hasClass(filterClass));
		    });
	  	}

	  	var value = $("#historySearchBar").val().toLowerCase();
	  	$("#displayTable > tbody > tr:visible ").filter(function() {
	      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
	    });
	  }


	  $("#historyClearSearch").click(function () {
		$("#historySearchBar").val("");
		$("#historyStatus").val("all");
		$("#historyShow").val("all");
		$("#displayTable > tbody > tr").show()
	  });

		$("#acntClearSearch").click(function () {
			$("#acntSearchBar").val("");
			$("#acntType").val("All");
		  	$("#displayTable > tbody > tr:not(#new_row)").show();
		})
});