var arrayMensajes = Array();
jQuery(document).ready(function ($) {
	M.AutoInit();

	$('a.sidenav-trigger-lg').click(function (e) {
		$('body').toggleClass('sidenav-open');
		$('#slide-out').removeAttr('style');
	});

	$('[data-select-img]').click(function (event) {
		var name = $(this).find('.img').attr('data-name');
		var target = $('#modal1').attr('data-field');
		$('#' + target).val(name);
		$('#modal1').closeModal();
	});
	$("#slide-out").niceScroll();
	fn_navigate_files();
	fn_delete_data();
	fn_change_state();
});

var fn_navigate_files = function (argument) {
	if ($('[data-navigatefiles="active"]').length) {
		fngetDir($('.gallery').attr('data-active-dir'));
		$('[data-expected]').click(function (event) {
			$('[data-expected]').attr('data-expected', 'inactive');
			$(this).attr('data-expected', 'active');
		});
	}
}

function fngetDir(strdir) {
	$('.gallery').attr('data-active-dir', strdir);
	$.ajax({
		url: base_url + 'admin/archivos/ajaxfnGetdir',
		type: 'POST',
		dataType: 'json',
		data: { 'directorio': strdir },
	})
		.done(function (json) {
			var html = '<div class="col m3 s4 dir">'
				+ '<div class="card"><div class="card-content">'
				+ 'UP</div>'
				+ '<div class="card-action">'
				+ '<span class="grey-text text-darken-4 dirname">'
				+ json['_parentdir'] + '</span></div>'
				+ '</div></div>';
			for (var i = json.files.length - 1; i >= 0; i--) {
				var reg = /\.[0-9a-z]{1,5}$/i;
				if (!json.files[i].match(reg) && json.files[i] != '.' && json.files[i] != '..') {
					html += '<div class="col m3 s4 dir">'
						+ '<div class="card"><div class="card-content">'
						+ '<i class="material-icons">folder</i><br />'
						+ '</div>'
						+ '<div class="card-action">'
						+ '<span class="grey-text text-darken-4 dirname">'
						+ strdir + '/' + json.files[i] + '</span></div>'
						+ '</div></div>';
				} else {
					if (json.files[i] != '.' && json.files[i] != '..') {
						var strFilename = json.files[i]
						if (strFilename.search('.jpg') > -1 || strFilename.search('.png') > -1 || strFilename.search('.gif') > -1) {
							html += '<div class="col m3 s4 getback"><div class="card"><div class="card-image">' +
								'<img class="activator" src="' + base_url + strdir + '/' + json.files[i] + '">' +
								'</div><div class="card-action strFilename" data-back-data="' + strdir.substring(2) + '/' + json.files[i] + '">' + json.files[i] + '</div></div></div>';
						} else {
							html += '<div class="col m3 s4 file">'
								+ '<div class="card"><div class="card-content">'
								+ '<i class="material-icons">description</i><br />'
								+ '</div>'
								+ '<div class="card-action">'
								+ '<span class="grey-text text-darken-4 dirname">'
								+ json.files[i] + '</span></div>'
								+ '</div></div>';
						}
					}
				}
			}
			$('.gallery').html(html);
			$('.gallery .dir').click(function (event) {
				var strdirectorio = $(this).find('.dirname').html();
				fngetDir(strdirectorio);
			});
			fnSetBackFile();
			console.log("success");
		})
		.fail(function () {
			console.log("error");
		})
		.always(function () {
			console.log("complete");
		});
}

var fnSetBackFile = function () {
	$('.getback').click(function (event) {
		var element = $(this);
		var strFilename = element.find('.strFilename').attr('data-back-data');
		$('[data-expected="active"]').val(strFilename);
		$('[data-expected]').attr('data-expected', 'inactive');
		$('#modal1').closeModal();
	});
}

var fn_delete_data = function () {

	//Set the target event
	$('[data-ajax-action]').click(function (event) {
		$('[data-ajax-action]').attr('data-ajax-action', 'inactive');
		$(this).attr('data-ajax-action', 'active');
	});
	//Set the acept event
	$('[data-action="acept"]').click(function (event) {
		var elementref = $('[data-ajax-action=active]');
		$.ajax({
			url: base_url + elementref.attr('data-url'),
			type: 'POST',
			dataType: 'json',
			data: $.parseJSON(elementref.attr('data-action-param')),
		})
			.done(function (json) {
				if (elementref.attr('data-url-redirect')) {
					window.location = base_url + elementref.attr('data-url-redirect');
				} else {
					if (json.result) {
						$(elementref.attr('data-target-selector')).remove();
					}
					Materialize.toast(json.message, 4000);
				}
			})
			.fail(function () {
				console.log("error");
			});
	});
}

var fn_change_state = function () {
	$('.change_state').change(function (event) {
		var elementref = $(this);
		var dta = $.parseJSON(elementref.attr('data-action-param'));
		dta['status'] = '0';
		if ($('.change_state').is(':checked')) {
			dta['status'] = '1';
		};
		console.log(dta);
		$.ajax({
			url: base_url + elementref.attr('data-url'),
			type: 'POST',
			dataType: 'json',
			data: dta,
		})
			.done(function (json) {
				if (elementref.attr('data-url-redirect')) {
					window.location = base_url + elementref.attr('data-url-redirect');
				} else {
					Materialize.toast(json.message, 4000);
				}
			})
			.fail(function () {
				console.log("error");
			});
	});
}

// Check that service workers are supported
if ('serviceWorker' in navigator) {
	// Use the window load event to keep the page load performant
	window.addEventListener('load', () => {
		navigator.serviceWorker.register('/public/js/service-worker.js');
	});
}