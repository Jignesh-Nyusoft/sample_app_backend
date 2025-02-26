$(document).ready(function() {

$(document).on("submit", ".ajax_form", function (event) {
			event.preventDefault();

			var posturl=$(this).attr('action');
			var formid = $(this).attr('id');
			if(formid)
			var formid = '#'+formid;
			else
			var formid = ".ajax_form";

	$(this).ajaxSubmit({
			url: posturl,
			dataType: 'json',
			beforeSend: function(){
                    $('.grid-loader').show();
					$(".submit").attr("disabled", 'disabled');
					$(formid).find('.is-invalid').attr("placeholder", "");
					$(formid).find('.is-invalid').removeClass('is-invalid');
					$(formid).find('.alert').removeClass('alert-info').removeClass('alert-success').removeClass('alert-danger').fadeIn(5000);
					$(formid).find('.alert').addClass('alert-info');
					$(formid).find('.alert').show();
					$(formid).find('.ajax_message').html('<strong>Please Wait ! <i class="fa fa-spinner fa-spin" aria-hidden="true"></i></strong>');

			},
			success: function(response){
                            $('.grid-loader').hide();

                            $(".submit").removeAttr("disabled", 'disabled');
                            $(formid).find('.alert').removeClass('alert-info').removeClass('alert-success').removeClass('alert-danger').fadeOut(5000);
                            if (response.status == "success") {
								// alert("ok");
								toastr[response.msgType](response.msg, response.msgHead);
								$(formid).find('.alert').fadeIn();
								$(formid).find('.alert').addClass('alert-success').children('.ajax_message').html(response.success_msg);
								// $(formid).find('.alert').fadeOut();
                            }
                            else{
								$(formid).find('.alert').fadeIn();
								$(formid).find('.alert').addClass('alert-danger').children('.ajax_message').html(response.error_msg);
								$.each(response.errorArray, function(key, value) {

										if (!/\brequired\b/.test(value)){
											$(formid).find('.ajax_message').append('<br>'+value);
										}
										console.log(key + " => " + value);
										var placeH	=value;
										$(formid).find('input[name="' + key + '"], select[name="' + key + '"],textarea[name="' + key + '"]').attr("placeholder", placeH);
										$(formid).find('input[name="' + key + '"], select[name="' + key + '"],textarea[name="' + key + '"]').addClass('is-invalid');

								});
					}


					if(response.resetform) {
						$(formid).resetForm();
					}
                    if (response.slideToTop) {
                        $('html, body').animate({
                            scrollTop: $(formid).offset().top - 290
                        }, 800);
                    }
                    if (response.url)
                                {
                                        window.location.href = response.url;
                                }
                                if (response.selfReload)
                                {
                                    // window.location.reload();
                                    window.location.href = window.location;
                                }
                    if (response.redirect == 'yes') {
                        window.location.href = response.redirectUrl;
                    }

			},
			error: function(response) {
                $('.grid-loader').hide();
                $(".submit").removeAttr("disabled", 'disabled');
				alert('Server Error !');

      }

		});
	 return false;

	});

	$(document).on("click", ".alert .close", function (event) {
			$(this).closest(".ajax_report").hide();
			$(this).closest(".alert").hide();
	});

});

function slideToElement(element,position)
{
	var target = $(element);

	$('html, body').animate({
		scrollTop: target.offset().top-100
	}, 500);
}

function slideToDiv(element)
{
	$("html, body").animate({scrollTop: $(element).offset().top-50 }, 1000);
}

function slideToTop()
{
	$("html, body").animate({scrollTop: 50}, 1000);
}

function isset(variable)
{
	if(typeof(variable) != "undefined" && variable !== null)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function hide_alert_message()
{
	setTimeout(function() {
		$('.alert.alert-dismissable').fadeOut(1000);
	}, 3000);
}
