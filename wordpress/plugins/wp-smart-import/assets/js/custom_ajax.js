jQuery( document ).ready(function( $ ){
	$.QueryString = (function (a) { // get query-string parameter
	    if (a == "") return {};
	    var b = {};
	    for (var i = 0; i < a.length; ++i) {
	        var p = a[i].split('=');
	        if (p.length != 2) continue;
	        b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
	    }
	    return b;
	})(window.location.search.substr(1).split('&'));
	
	// Get Current Id
	var ID = $('input[name=id]').val();
	//create array to number of range 
	Array.range = function(n) {
	  	// Array.range(5) --> [0,1,2,3,4]
		return Array.apply(null, new Array(n)).map(function (empty, index) {
	        return index;
	    });
	};
	//chunk array
	Object.defineProperty(Array.prototype, 'chunk', {
	  	value: function(n) {
	    	// ACTUAL CODE FOR CHUNKING ARRAY:
	    	return Array.range(Math.ceil(this.length/n)).map((x,i) => this.slice(i*n,i*n+n));
	  	}
	});

	function timerRedirect(page = 1) {
	    var timer = 10;
	    var interval = setInterval(function() {
	        timer--;
	    	$('.timer').text(timer);
	        if (timer === 0) {
	            clearInterval(interval);
	            window.location = path['admin_url']+'?page='+path['pages'][page];
	        } 
	    }, 1000);
	}
    $('#file-upload').click(function(event) {
		event.stopPropagation();
	});
	$('#file-upload').on('change', prepare_upload);
	var  tempVal ='';
	function prepare_upload(event) {
		var file = event.target.files;
	  	var parent = $("#" + event.target.id).parent();
	  	var sendData = new FormData();
	  	sendData.append("_nonce", _nonce); 
	  	sendData.append("action", "wpsi_file_upload");
	    sendData.append("wpsi_file_upload", file[0]);
	   	val = $(this).val();
	   	$(this).val('');
    	$.ajax({
		  	url: ajaxurl,
          	type: 'POST',
          	data: sendData,
          	cache: false,
          	dataType: 'json',
          	processData: false, // Don't process the files
          	contentType: false, // Set content type to false as jQuery will tell the server its a query string request
          	beforeSend: function() { $('#ajax-wait').show(); $('.wpsi-post-container').slideUp(); },
    		complete: function() { $('#ajax-wait').hide(); },
          	success: function(data, textStatus, jqXHR) {
  	 	    	if (data.response == 'SUCCESS') {
  	 	    		tempVal = val;
  	 	    		$('.wpsi-post-container').slideDown();
  	 	    		$('#wpsi_file_path').val(data.filepath);
   					$('#upload_msg').show().text('Uploaded Complete : '+data.filename+' ('+data.file_size+')');
  	 	    	} else {
   					$('#upload_msg').show().text('Error : '+data.msg);
  	 	    	}
          	}
		});
	}

	///// Get XML Node Priviwe with ajax-call
	var tg = $("#element_input");
	var node = tg.val();
	var cnt = tg.data('cnt');
	var xmlElementList = $('.xml_element_list');
 	if (xmlElementList.find("a").length > 0) {
 		xmlElementList.find("a").first().addClass("selected");
 		node = xmlElementList.find("a").attr('rel');
 		cnt = $.trim(xmlElementList.find("a").data('count'));
 		$('#input_node').val(node);
 		$('#input_nodecount').val(cnt);
 		node_previwe(node, cnt);
 	}
	$('.xml_element_list .wpsi-root-element').click(function() {
		$('.xml_element_list .wpsi-root-element').removeClass('selected');
		node = $(this).attr('rel');
		cnt = $.trim($(this).data('count'));
		$(this).addClass('selected');
 		$("#input_node").val(node);
 		$('#input_nodecount').val(cnt);
 		event = "click";
		node_previwe(node, cnt, 1, event);
	});

	if($('#wpsi-nodes-preview-sticky').length > 0) { // for template page
		node_previwe(node, cnt, 1);
	}
	$('.wpsi-nodes-preview').on('click', '.wpsi-xml-preview-pagination', function(e) {
        e.preventDefault();	
        var node_num = $(this).attr('data-val');
		node_previwe(node, cnt, node_num);
	});
	$('.wpsi-nodes-preview').on('change', '#node_num', function(e) {
		var node_num = $(this).val();
		node_previwe(node, cnt, node_num);
	});
	function node_previwe(node, cnt, node_num = 1, event = '') {
		var id = $.QueryString["id"]||0;
		var sendData = {
			id: id, 
			action: 'wpsi_xml_preview', 
			node: node, 
			count: cnt,
			node_num: node_num,
			_nonce: _nonce
		};
		$.ajax({
		  	url: ajaxurl,
          	type: 'POST',
          	data:sendData,
          	cache: false,
          	dataType: 'json',
          	beforeSend: function() {
          		if (event == 'click') {
          			$('#ajax-wait').show(); 
          		} else {
          			$('.wpsi-nodes-preview').addClass('lock');
          		}
          	},
          	success: function(data, textStatus, jqXHR) {
          		$('.wpsi-nodes-preview').html(data.content);
          		$('#wpsi-nodes-preview-sticky').tag();
          	}
		}).done( function() { 
			$('.wpsi-nodes-preview').removeClass('lock');
			$('#ajax-wait').hide();
		});
	}
	$('#form-id').on('load',function() {
		var id = $.QueryString["id"]||0;
		if (id != 0) {
			$('chech_id').trigger('change');			
		}
	});
	//// End Node preview

	/// Image Preview 
	$('.preview-image').on('click', '.wpsi-image-preview-pagination', function(e) {
        e.preventDefault();
    	node_num = $(this).data('val');
		image_previwe(cnt, node_num);
	});
	$('.preview-image').on('change', '#image_node_num', function(e) {
		var node_num = $(this).val();
		node_num = parseInt(node_num) > 0 ? node_num : 1;
		image_previwe(cnt, node_num);
	});
	$('#image_preview').click(function(e) {
		e.preventDefault();
		image_previwe(cnt, 1, 'click');
	});
	function image_previwe(cnt, node_num = 1, event = '') {
		var formData =  $('#wpsi_template_form').serialize();
		var id = $.QueryString["id"]||0;
		var sendData = {
			id : id,
			action : 'wpsi_images_preview',
			formData : formData,
			count : cnt,
			node_num : node_num,
			_nonce : _nonce
		};
		$.ajax({
		  	url: ajaxurl,
          	type: 'POST',
          	data:sendData,
          	cache: false,
          	dataType: 'json',
          	beforeSend: function() {
          		if (event == 'click') {
          			$('#ajax-wait').show(); 
          			$('.preview-image').html('');
          		} else {
          			$('.preview-image').addClass('lock');
          		} 
          	},
    		complete: function() {
    			$('.preview-image').removeClass('lock');
    			$('#ajax-wait').hide(); 
    		},
          	success: function(data, textStatus, jqXHR) {	
          		$('.preview-image').html(data.content);
          	}
		});
	}
	$("body").on("click" ,'.add_term', function(e) {
		e.preventDefault();
		var input = $(this).prev('input').first();
		if (input.val() !='') {
			var addin = $(this).data('addin');
			var sendData = new FormData();
		  	sendData.append('_nonce',_nonce);
		  	sendData.append('action','insert_term');
		  	sendData.append('term_names', input.val());
		  	sendData.append('taxonomy_name', addin);
		  	if (ID != 0 || ID != null)
		  		sendData.append('id', id);

			$.ajax({
			  	url: ajaxurl,
	          	type: 'POST',
	          	data:sendData,
	          	cache: false,
	          	dataType: 'json',
	          	processData: false,
				contentType: false,
	          	beforeSend: function() { $('#ajax-wait').show(); $('.preview-image').html(''); },
        		complete: function() { $('#ajax-wait').hide(); },
	          	success: function(data, textStatus, jqXHR) {
	          		var array = data.content;
	          		for (var i = 0; i < array.length; i++) {
					  	if (array[i]['status'] == 'error') {
					    	toastr.error(array[i]['msg'], array[i]['title']);
					    } else {
					    	$(".wpsi_"+addin).css('display','block');
					   	 	$(".wpsi_"+addin).append('<a href="javascript:void(0)" class="post_tax">'+array[i]['title']+'</a>');
					    	toastr.success(array[i]['msg'], array[i]['title']);	
					    }
					}
	          	}
			});
		}
	});

	$( ".file-input" ).bind("keyup change", function(e) {
		var classList = $(this).prop("classList");
		if ($.trim( $(this).val() ) == $.trim(tempVal) && tempVal !='') {
			if($.inArray("download_file", classList) !== -1) {
				$('#download_file').attr('disabled', 'disabled').addClass('disabled');
			}
			$('#upload_msg').slideDown();
			$('.wpsi-post-container').slideDown();
		} else if($.inArray("select-file", classList) !== -1) {
			if ($(this).val() !='' ) {
				var filedata = $(this).val().split('/');
				$('#wpsi_file_path').val($(this).val());
				$('#upload_msg').show().text('File Name : '+filedata[1]);
				$('.wpsi-post-container').slideDown();
			} else {
				$('#upload_msg').slideUp();
				$('.wpsi-post-container').slideUp();
			}
		} else {
			$('#download_file').removeAttr('disabled').removeClass('disabled');
			$('#upload_msg').slideUp();
			$('.wpsi-post-container').slideUp();
		} 
	});
	$("body").on("click" ,'#download_file', function(e) {
		e.preventDefault();
		var input = $('.download_file').val();
		if (input != '') {
			var sendData = new FormData();
		  	sendData.append("action", "wpsi_file_upload");
		    sendData.append("file_from", 'download');
		    sendData.append("file", $.trim(input));
		    sendData.append("_nonce", _nonce);
	    	$.ajax({
    		  	url: ajaxurl,
	          	type: 'POST',
	          	data: sendData,
	          	cache: false,
	          	dataType: 'json',
	          	processData: false, // Don't process the files
	          	contentType: false, // Set content type to false as jQuery will tell the server its a query string request
	          	beforeSend: function() { $('#ajax-wait').show(); $('.wpsi-post-container').slideUp();},
        		complete: function() { $('#ajax-wait').hide(); },
	          	success: function(data, textStatus, jqXHR) {
	  	 	    	if(data.response == 'SUCCESS') {
	  	 	    		$('#download_file').attr('disabled', 'disabled');
	  	 	    		tempVal = input;
	  	 	    		toastr.success(data.msg);
	  	 	    		$('.wpsi-post-container').slideDown();
  	 	    			$('#wpsi_file_path').val(data.filepath);
   						$('#upload_msg').show().html('Download Complete : '+data.filename+' ('+data.file_size+')');
	  	 	    	} else {
					    toastr.error(data.msg);
	  	 	    	}
	          	}
			});
		}
	});
	$("#file_name_check").click(function(e){
		e.preventDefault();
		var input = $(this).prev('input').val();
		if (input != '') {
			var sendData = new FormData();
		  	sendData.append("action", "wpsi_file_name_check");
		    sendData.append("name", $.trim(input));
		    sendData.append("_nonce", _nonce);
		    _this = $(this);
			$.ajax({
    		  	url: ajaxurl,
	          	type: 'POST',
	          	data: sendData,
	          	cache: false,
	          	dataType: 'json',
	          	processData: false, // Don't process the files
	          	contentType: false, // Set content type to false as jQuery will tell the server its a query string request
	          	beforeSend: function() { $('#ajax-wait').show();},
        		complete: function() { $('#ajax-wait').hide(); },
	          	success: function(data, textStatus, jqXHR) {	
	  	 	    	if(data.response == 'success') {
	  	 	    		toastr.success(data.msg);
	  	 	    	} else {
					    toastr.error(data.msg);
	  	 	    	}
	          	}
			});
		}
	});
	var batches = {},bl=0;
	$('#run_import').click(function(){
		batches =  Array.range(parseInt(cnt)).chunk(3);
		bl = batches.length;
		if(bl>0)
			run_import();
	});
	var created = updated = failed = count = 0 ;
	function run_import(batch_no = 0) {
		$(".wpsmartimport-plugin #run_import").attr('disabled','disabled').text('Please Wait...');
		var id = $.QueryString["id"]||0;
		var sendData = {
				'id' 			: id, 
				'action'		: 'wpsi_runImport',
				'batches'		: batches,
				'batch_no'		: batch_no,
				'total_batch'	: bl,
				'_nonce'		: _nonce
			};
		if (id != 0) {
			$.ajax({
			  	url: ajaxurl,
	          	type: 'POST',
	          	data:sendData,
	          	cache: false,
	          	dataType: 'json',
	          	success: function(data, textStatus, jqXHR) {
	          		if (data.status == 'success') {
	          			created += data.created;
	          			updated += data.updated;
	          			failed += data.failed;
		          		count += data.count;
	          			var response_div = $('#response-content');
	          			var Progress = $('.meter');
	          			
	          			response_div.show();
	          			Progress.show();
	          			
	          			post_type = data.post_type
	          			post_type = post_type.charAt(0).toUpperCase() + post_type.slice(1);
	          			width = Math.floor(data.new_width) < 1 ? data.new_width : Math.floor(data.new_width);
	          			response_div.find('.flex-container').html("<div>Post Type <br/><b>"+ post_type +"</b></div><div>Created <br/><b>"+ created +"</b></div> <div>Updated <br/><b>"+ updated +"</b></div><div>Failed <br/><b>"+ failed +"</b></div>");
		          		Progress.find('.progress-text').animate({'width':''+width+'%'}, 100).html(width+'% <span></span>');
		          		if (data.proccessing == 'run') { 
	          				run_import(data.batch_no);
		          		} else {
		          			Progress.hide();
		          			$('#run_import').removeAttr('disabled').html('Run Import');
	          				response_div.find('.flex-container').append( "<div>Count <br/><b>"+ count +"</b></div>" );
		          		}
	          		} else {
	          			toastr.error(data.msg);
	          			$('#run_import').removeAttr('disabled').html('Run Import');
	          		}
	          	},
	          	error: function (xhr, ajaxOptions, thrownError) {
           			console.log(xhr.status);
		           	console.log(xhr.responseText);
		           	console.log(thrownError);
		       	}
			});
		}
	} // End run_import Function

	// Start delete_import Function
	var del_post = del_import = del_file = total_del_post = idslength = width = counter = total_batch = total_post = width_per_pices = 0 ;
	var id = $.QueryString["id"]||0;
	var ids = [];
	var  post_type ='' ;
	$('#delete_imports').click(function() {
		$confirm = confirm('Are you sure Want to Delete Import?');
		if ($confirm) {
			$("html, body").animate({ scrollTop: 0 }, 800);
			$(this).attr('disabled','disabled').text('Please Wait...');
			if (id !=0) {
				ids = id.split(','); 
				idslength = ids.length;
				getTotalbatch(ids[0]);
				delete_import(ids[0]);
			}
		}
	});

	function getTotalbatch(id) {
		var sendData = {
			'id' : id,
			'action':'get_total_batch_for_import',
			'_nonce':_nonce
		};
		$.ajax({
		  	url: ajaxurl,
          	type: 'POST',
          	data:sendData,
          	cache: false,
          	dataType: 'json',
          	success: function(data, textStatus, jqXHR) {
          		width_per_pices =  100 / parseInt( data.total_batch);
          	}
	    });
	}

	function delete_import(id) {
		status1 = $("#delete_import").is(":checked") ;
		status2 = $("#delete_post").is(":checked");
		var formData = $('#wpsi-delete-import').serialize();
		var sendData = {
			'id':id, 
			'action':'manage_imports', 
			'formData':formData, 
			'_nonce':_nonce  
		};
		if (idslength != 0) {
			$.ajax({
			  	url: ajaxurl,
	          	type: 'POST',
	          	data:sendData,
	          	cache: false,
	          	dataType: 'json',
	          	success: function(data, textStatus, jqXHR) {
	          		data.id = parseInt(data.id);
	          		if(data.status == 'success') {
          				$('#Response-content').show();
		          		width += width_per_pices;
          				width = Math.floor(width) < 1 ? width : Math.floor(width);
		          		var Progress = $('.meter');
		          		Progress.show();
		          		if(data.post_type != null) {
		          			post_type = data.post_type;
		          		}
          				post_type = post_type.charAt(0).toUpperCase() + post_type.slice(1);
          				del_post += data.deleted_post;
          				Progress.find('.progress-text').animate({'width':''+width+'%'}, 100).html(width+'% <span></span>');
						if (status2) {
          					total_del_post += data.deleted_post;
			          		if (id == data.id && counter == 0) {
								counter++;	
			          			$('#Response-content').append("<p>"+del_post +'  '+post_type+' Deleted Import Id : '+ data.id +"</p>");
			          		} else {
			          			$('#Response-content').find('p').last().html(del_post +'  '+post_type+' Deleted Import Id : '+ data.id );
			          		}
		          		}
			          	if (data.proccessing == 'run') { 
			          		delete_import(data.id);
		          		} else {
		          			del_post = 0;
		          			Progress.find('.progress-text').animate({'width':'100%'}, 100).html('100% <span></span>');
		          			del_import += data.deleted_import;
		          			index = ids.indexOf(data.id.toString());
		          			if (status1) {
		          				$('#Response-content').append("<p>Import Deleted Id : "+ data.id +"</p>");
		          			}
		          			if (idslength-1 > index) {
		          				if (status2) {
		          					$('#Response-content').append("<hr><p></p>");
		          				}
			          			counter = 0; 
		          				getTotalbatch(ids[index+1]);
		          				delete_import(ids[index+1]);
		          			} else {
		          				Progress.hide();
		          				$('#Response-content').append('<div class="text-center"><h1> Task Completed Successfully </h1> <h1> Total '+del_import+' Import And '+ total_del_post+' Post Deleted </h1> <h1 > Redirect After <span class="timer"> </span> </h1></div>');
		          				timerRedirect();
		          			}
		          			if (width == 100) {
		          				width = 0
		          			}
		          		}	
	          		} else {
	          			toastr.error(data.msg);
	          		}
	          	},
	          	error: function (error) {
	          		toastr.error(error.statusText);
				}
			});
		}
	} // End delete_import Function

	// Start delete_files Function
	var fileDeleteOption = '', statusCode;
	width_per_pices = 0;
	$('#delete_files').click(function() {
		$confirm = confirm('Are you sure Want to Delete Import ?');
		if ($confirm) {
			$('#manage-file-record').hide().find('tbody').html('');
			$("html, body").animate({ scrollTop: 0 }, 600);
			var formData =  $('#wpsi-delete-file').serializeArray(); 
			$(this).attr('disabled','disabled').text('Please Wait...');
			if (id != 0) {
				ids = id.split(','); 
				idslength = ids.length;
				fileDeleteOption = $("input[name='manage_file[delete]']:checked").val();
				switch(fileDeleteOption) {
				    case 'record':
				       	statusCode = 1; 
				        break;
				    case 'import':
				        statusCode = 2;
				        break;
				    case 'post':
				        statusCode = 3;
				        break;
				    case 'full':
				        statusCode = 4;
				        break;
				}
				if (statusCode != 1) {
					getTotalbatchforFile(ids[0]);
				} else {
					width_per_pices =  100 / parseInt(idslength);
				} 
				delete_files(ids[0]);
			}			
		}
	});
	var total_post = total_import = 0;
	function getTotalbatchforFile(id) {
		width_per_pices = 0;
		fileDeleteOption = $("input[name='manage_file[delete]']:checked").val();
		var sendData = {
			'id' : id, 
			'action':'get_total_batch_for_file',
			'_nonce':_nonce
		};
		$.ajax({
		  	url: ajaxurl,
          	type: 'POST',
          	data:sendData,
          	cache: false,
          	dataType: 'json',
          	success: function(data, textStatus, jqXHR) {
      			total_batch = data.total_batch;
      			total_post = data.post;
      			total_import = data.import;
          		if (data.total_batch == 0) {
          			width_per_pices = 100;
          		}
          		if (statusCode == 2) {
          			width_per_pices = 100 / parseInt(total_import);
          		} else {
          			width_per_pices = 100 / parseInt(total_batch);
          		}
          	},
			error: function (error) {
          		toastr.error(error.statusText);
			}
	    });
	}
	var $append_tr = '<tr> <td></td> <td></td> <td></td> <td></td> </tr>';
	var t_post = t_import = t_file = 0;
	function delete_files(id, idx=0) {
		if (idslength != 0) {
			var sendData = {
				'id' 			: id, 
				'idx' 			: idx, 
				'statusCode' 	: statusCode, 
				'action'		: 'manage_import_files',
			 	'_nonce'		: _nonce
			};
			$.ajax({
			  	url		: ajaxurl,
	          	type 	: 'POST',
	          	data 	: sendData,
	          	cache 	: false,
	          	dataType: 'json',
	          	success: function(data, textStatus, jqXHR) {
	          		data.id = parseInt(data.id);
	          		if (data.status == 'success') {
	          			$('#Response-content').show();
	          			var Progress = $('.meter');
	          			Progress.show();
		          		manage_file_tab = $('#manage-file-record');
						manage_file_tab.show().removeClass('display-none');
	          			index = ids.indexOf(data.id.toString());
	          			if (id == data.id && counter == 0) { // append new tr
          					manage_file_tab.find('tbody').append($append_tr);
          					counter++; 
	          			} 
	          			if (statusCode == 1) {
	          				findTD = manage_file_tab.find('tbody tr').last().find('td');
	          				cnt_file = data.deleted_file == 0 ? 0 : data.deleted_file;
	          				findTD.eq(0).html(data.file_name);
	          				findTD.eq(1).html( cnt_file );
	          				findTD.eq(2).html('0');
	          				findTD.eq(3).html('0');
	          				width += width_per_pices;
							del_file += data.deleted_file;
	          			} else if (statusCode == 2) {
	          				del_post += data.deleted_post;
	          				del_import += data.deleted_import;
	          				if (data.deleted_import != 0) {
	          					total_import--;
		          				if (total_import <= 0) {
		          					width = 100;
		          				} else {
	          						width += width_per_pices;
		          				}
	          				}
	          				findTD = manage_file_tab.find('tbody tr').last().find('td');
	          				findTD.eq(0).html(data.file_name);
	          				findTD.eq(1).html('0');
	          				findTD.eq(2).html(del_import);
	          				findTD.eq(3).html('0');
	          			} else if (statusCode == 3) {
	          				total_batch--;
	          				if (total_batch <= 0) {
	          					width = 100;
	          				} else {
	          					width += width_per_pices;
	          				} 
	          				del_post += data.deleted_post;
	          				findTD = manage_file_tab.find('tbody tr').last().find('td');
	          				findTD.eq(0).html(data.file_name);
	          				findTD.eq(1).html('0');
	          				findTD.eq(2).html('0');
	          				findTD.eq(3).html(del_post);
	          			} else if (statusCode == 4) {
	          				del_post += data.deleted_post;
	          				del_import += data.deleted_import;
	          				if (data.deleted_import != 0) {
	          					total_import--;
		          				if (total_import <= 0) {
		          					width = 100;
		          				} else {
	          						width += width_per_pices;
		          				}
	          				}
	          				findTD = manage_file_tab.find('tbody tr').last().find('td');
	          				findTD.eq(0).html(data.file_name);
	          				findTD.eq(1).html('0');
	          				findTD.eq(2).html(del_import);
	          				findTD.eq(3).html(del_post);
	          			}
		          		width = Math.floor(width) < 1 ? width : Math.floor(width);
          				Progress.find('.progress-text').animate({'width':''+width+'%'}, 100).html(width+'% <span></span>');
	          			if (data.proccessing == 'run') { 
			          		delete_files(data.id, data.idx);
		          		} else {
		          			t_import += del_import;
		          			t_post += del_post ;
			          		counter = 0;
	          				if (idslength-1 > index) {
			          			if (statusCode != 1) {
			          				getTotalbatchforFile(ids[index+1]);
			          			}
			          			del_post = del_import = 0;
		          				delete_files(ids[index+1]);
		          			} else {
		          				manage_file_tab.find('tfoot').append("<th> Total </th><th>"+del_file+"</th><th>"+t_import+"</th><th>"+t_post+"</th>");
		          				$('#Response-content').append('<div class="text-center"><h1> Task Completed Successfully </h1>  <h1 > Redirect After <span class="timer"> 10 </span> </h1></div>');
		          				timerRedirect(2);
		          				Progress.hide();
		          			}
		          			if (width==100) {
		          				width = 0
		          			}
		          		}
	          		} else {
	          			toastr.error(data.msg);
	          		}
	          	}
	        });
	    }
	}
});