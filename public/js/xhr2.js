// Ajax File upload with jQuery and XHR2
// Sean Clark http://square-bracket.com
$.fn.upload = function(remote,data,successFn,progressFn) {
	// if we dont have post data, move it along
	if(typeof data != "object") {
		progressFn = successFn;
		successFn = data;
	}
	return this.each(function() {
		if($(this)[0].files[0]) {
			var formData = new FormData();
			var $input = $(this);
			
			// add 1
			if($(this)[0].files.length == 1) {
				formData.append($(this).attr("name"), $(this)[0].files[0]);
			
			// add many files
			} else {
				$.each($(this)[0].files, function(i, file) {
					formData.append($input.attr("name")+"[]", file);
				});
			}
			
			// if we have post data too
			if(typeof data == "object") {
				for(var i in data) {
					formData.append(i,data[i]);
				}
			}
			console.log("data",formData);
			// do the ajax request
			$.ajax({
				url: remote,
				type: 'POST',
				xhr: function() {
					myXhr = $.ajaxSettings.xhr();
					if(myXhr.upload && progressFn){
						myXhr.upload.addEventListener('progress',function(prog) {
							console.log("prog2",prog);
							var value = ~~((prog.loaded / prog.total) * 100);
							
							// if we passed a progress function
							if(progressFn && typeof progressFn == "function") {
								progressFn(prog,value);
							
							// if we passed a progress element
							} else if (progressFn) {
								$(progressFn).val(value);
							}
						}, false);
					}
					return myXhr;
				},
				data: formData,
				cache: false,
				timeout:0,
				contentType: false,
				processData: false,
				complete : function(res) {
					if(successFn) successFn(res.responseText);
				}
			});
		}
	});
}