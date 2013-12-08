// JavaScript Document
jQuery(function($){
	var processFile = "assets/inc/ajax.inc.php",
	eventManager = {	
		"removeevent" : function(){
			$(".active").fadeOut("slow", function(){
				$(this).remove();
			});
		},
		
		"addevent" : function(data, formData){
			// Converts the query string to an object
			var entry = $.fx.deserialize(formData),
				cal = new Date(NaN),
				
				// Makes a date object for the new event
				event = new Date(NaN),
				
				// Extracts the calendar month from the H2 ID
				cdata = $("h2").attr("id").split('-'),
				
				// Extracts the event day, month, and year
				date = entry.event_start.split(' ')[0],
				
				// Splits the event data into pieces
				edata = date.split('-');
				
			// Sets the date for the calendar date object
			cal.setFullYear(cdata[1], cdata[2], 1);
			
			// Sets the date for the event date object
			event.setFullYear(edata[0], edata[1], edata[2]);
			
			// Since the date object is created using
            // GMT, then adjusted for the local timezone,
            // adjust the offset to ensure a proper date
			event.setMinutes(1);
			
			// If the year and month match, start the process
            // of adding the new event to the calendar
			if ( cal.getFullYear()==event.getFullYear() && cal.getMonth()==event.getMonth() ){
				// Gets the day of the month for event
				var day = String(event.getDate());
				// Adds a leading zero to 1-digit days
				day = day.length==1 ? "0"+day : day;
				$("<a>")
					.hide()
					.attr("href", "view.php?event_id="+data)
					.text(entry.event_title)
					.insertAfter($("strong:contains("+day+")"))
					.delay(1000)
					.fadeIn("slow");
			}
		},
		
		"editevent" : function(data, formData){
			//
			var entry = $.fx.deserialize(formData),
				cal = new Date(NaN),
				// Makes a date object for the new event
				event = new Date(NaN),
				// Extracts the calendar month from the H2 ID
				cdata = $("h2").attr("id").split('-'),
				// Extracts the event day, month, and year
				date = entry.event_start.split(' ')[0],
				// Splits the event data into pieces
				edata = date.split('-');
				
				// Sets the date for the calendar date object
			cal.setFullYear(cdata[1], cdata[2], 1);
			// Sets the date for the event date object
			event.setFullYear(edata[0], edata[1], edata[2]);
			event.setMinutes(1);
			if ( cal.getFullYear()==event.getFullYear() && cal.getMonth()==event.getMonth() ){
				// Gets the day of the month for event
				var day = String(event.getDate());
				// Adds a leading zero to 1-digit days
				day = day.length==1 ? "0"+day : day;
				
				//Hides the old value 
				$("strong:contains("+day+")").siblings("a").hide();
				
				//Shows the updated value
				$("<a>")
					.hide()
					.attr("href", "view.php?event_id="+data)
					.text(entry.event_title)
					.insertAfter($("strong:contains("+day+")"))
					.delay(1000)
					.fadeIn("slow");
			}
		}
	};
	
	var modalWindow = $.fx.initModalWindow(); //modalWindow的值就是$('modal-window')
	var modalOverlay = $.fx.initModalOverlay();//modalOveraly的值就是$('modal-overlay')
	var modalWindowContent = $('.modal-window-content');
	
	// Pulls up events in a modal window
	$("li>a").live("click", function(event){
		event.preventDefault();
		$(this).addClass("active");
		var data = $(this).attr("href").replace(/.+?\?(.*)$/, "$1");
			  		
		// Loads the event data from the DB  
		$.ajax({
		  type: "POST",
		  url: processFile,
		  data: "action=event_view&" + data,
		  success: function(data){
		    $.fx.boxin(data,modalWindow,modalOverlay);
		  },
		  error: function(msg) {
		    modalWindow.append(msg);
		  }
		});	
	});
	
	// Displays the edit form as a modal window,这里必须要用live()，而不是bind()绑定事件，为什么？我也说不出所以然。
	$(".admin-options form,.admin").live("click", function(event){
		// Prevents the form from submitting
		event.preventDefault();
		
		// Loads the action for the processing file
		var action = $(event.target).attr("name") || "edit_event";
		id = $(event.target).siblings("input[name=event_id]").val();
		id = ( id!=undefined ) ? "&event_id="+id : "";
		
		// Loads the editing form and displays it
		$.ajax({
			type: "POST",
			url: processFile,
			data: "action="+action+id,
			success: function(data){
			    // Hides the form
			    var form = $(data).hide();
			    // Make sure the content of modal window must be removed
				$('.modal-window-content').children().remove();
			    $.fx.boxin(null,modalWindow,modalOverlay);
			    form
				    .appendTo(modalWindowContent)
				    .addClass("edit-form")
				    .fadeIn("slow");
			},
			error: function(msg){
			    alert(msg);
			}
		});
	});
	
	// Edits events without reloading
	$(".edit-form input[type=submit]").live("click", function(event){
		// Prevents the default form action from executing
		event.preventDefault();
		
		// Serializes the form data for use with $.ajax()
		var formData = $('.edit-form').serialize(),		
			// Stores the value of the submit button
			submitVal = $(this).val(),		
			remove = false,
			start = $(this).siblings("[name=event_start]").val(),
			end = $(this).siblings("[name=event_end]").val();
		
		// If this is the deletion form, appends an action
		if ( $(this).attr("name")=="confirm_delete" ){
		  
		  // Adds necessary info to the query string
		  formData += "&action=confirm_delete"+ "&confirm_delete="+submitVal;
		  
		  // If the event is really being deleted, sets
		  // a flag to remove it from the markup
		  if ( submitVal=="删除" ){
			remove = true;
		  }
		}
		if ( $(this).siblings("[name=action]").val()=="event_edit" ){
		  if ( !validDate(start) || !validDate(end) ){
			alert("Valid dates only! (YYYY-MM-DD HH:MM:SS)");
			return false;
		  }
		}
		
		// Sends the data to the processing file
		$.ajax({
			type: "POST",
			url: processFile,
			data: formData,
			success: function(data) {
				// If this is a deleted event, removes it from the markup
				if ( remove===true ){
				  eventManager.removeevent();
				}
				
				// Fades out the modal window
				$.fx.boxout();
				
				// If this is a new event, adds it to the calendar
				if ( $("[name=event_id]").val().length==0 && remove===false ){
					eventManager.addevent(data, formData);
				}
				if ( $("[name=event_id]").val().length > 0 && remove===false ){
					eventManager.editevent(data, formData);
				}
			  },
			  error: function(msg){
				alert(msg);
			  }
		});
	});
	
	$(".edit-form a.cancel").live("click", function(event){
	   $.fx.boxout(event);
	});
	
	$(".loginAsAjax").bind("click",function(event){
		event.preventDefault(); 
		$.ajax({
		  type: "POST",
		  url: processFile,
		  data: "action=login_form" ,
		  success: function(data){
					$.fx.boxin(data,modalWindow,modalOverlay);
		  },
		  error: function(msg) {
				   modalWindow.append(msg);
		  }
		});			
	});	
});