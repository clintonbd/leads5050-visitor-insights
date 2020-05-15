/* Leads5050 v.1.0 */

(function($) {

    //API registration button
    if (varzl.hash && varzl.hash.length>5) {
        if ($("#leads5050_get_api_btn").length > 0) {
            var license_check = $("#leads5050-api-key-value").html();
            if (license_check.length == 5) {
                $("#leads5050_get_api_btn").text('Refresh license');
                $("#leads5050-api-key-value").show();
                $("#leads5050-main-api-form").show();
            } else {
                $("#leads5050_get_api_btn").removeClass("button-secondary");
                $("#leads5050_get_api_btn").addClass("button-primary");
            }
            $("#leads5050_get_api_btn").bind('click', function () {
                //fetch address for the api data on leads5050
                var api_url = 'https://leads5050.com/wp-json/ctasm/v1/api/' + varzl.hash + '/';
                console.log('API button clicked!!', api_url);
                $("#leads5050_btn_confirm").html('...Fetching data').fadeIn(100).fadeOut(1500);
                $.ajax({
                    url: api_url,
                    method: 'GET',
                    success: function (rst_data) {
                        console.log('success', rst_data);
                        var api_license = rst_data[1];
                        console.log('license', api_license);
                        $.ajax({
                            url: varzl.ajax_url,
                            method: 'POST',
                            data: {action: 'leads5050_set_license', api_license: api_license},
                            success: function () {
                                $("#leads5050_get_api_btn").text('Refresh Again');
                                $("#leads5050_get_api_btn").removeClass('button-primary');
                                $("#leads5050_get_api_btn").addClass('button-secondary');
                                //$("#leads5050_get_api_btn").hide();
                                $("#leads5050-api-key-value").text(api_license);
                                $("#leads5050-api-key-value").show();
                                $("#leads5050-main-api-form").show();
                                $("#leads5050_form_api_value").attr('value', api_license);
                                $("#leads5050_btn_confirm").html('Success').fadeIn(500).fadeOut(8000);
                                console.log('PHP Call', 'Option Updated');
                            },
                            error: function (xhr) {
                                $('#leads5050_btn_confirm').html('Error').fadeIn(500).fadeOut(5000);
                                console.log('ERROR', ('PHP call error occurred: ' + xhr.status + ' ' + xhr.statusText));
                            }
                        });
                        console.log('End', 'All done');
                    },
                    error: function (xhr) {
                        console.log('ERROR', ('REST call error occurred: ' + xhr.status + ' ' + xhr.statusText));
                    }
                });
            });
        }
    } else {
        console.log('ERROR', 'Missing API Seed value');
    }

    //Visitor data
    if ($("#leads5050_visit_container").length > 0) {
        // alert('Container found');
        if (varzl.hash.length > 5) {
            //alert('Variables found');
            var stamp = new Date(Date.now());
            var api = $("#leads5050-api-key-value").text();
            $("#leads5050_visit_container").html(info);
            if (api.length == 5){
                // alert('API: ' + api);
                var info = '<div class="leads5050_spinner"></div>';
                //** NB - there should be a check that the domain is valid over here - get this from the header **
                //get the data from the key url so that the licence needs never be exposed
                //combine the code into the REST API
                // e.g. http://vhost9/wordpress/wp-json/ctasm/v1/visit/aHR0cHM6Ly9jcmVhdG9yc2VvLmNvbS8=/
                var api_url = 'https://leads5050.com/wp-json/ctasm/v1/visit/' + varzl.hash + '/' + btoa(api) + '/';
                console.log('REST Data API', api_url);
                $.ajax({
                    url: api_url,
                    method: 'GET',
                    success: function (rst_data) {
                        console.log('success', rst_data);
                        $("#leads5050_visit_container").html('');
                        //Post this data to the PHP function and run the PHP script to draw data tables
                        var dicap_action = 'leads5050_visit_report';
                        $.ajax({
                            url: varzl.ajax_url,
                            type: 'post',
                            data: {action: dicap_action, rstData: rst_data},
                            dataType: 'json',
                            success : function(myData,status){
                                mytxt = myData['output'];
                                $("#leads5050_visit_container").html(myData['output']);
                            },
                            error: function (xhr, status, errorThrown) {
                                mytxt = '<h3>ERROR</h3><p>' + status + ' [' + errorThrown + ']<br />' + xhr.responseText + '</p>';
                                $("#leads5050_visit_container").html('<div>' + mytxt + '</div>');
                            }
                        });
                    },
                    error: function (xhr) {
                        console.log('ERROR', ('REST call error occurred: ' + xhr.status + ' ' + xhr.statusText));
                        $("#leads5050_visit_container").html("There was a problem recovering data for this site");
                    }
                });
            } else {
                console.log('ERROR', 'API key not set');
                $("#leads5050_visit_container").html("Please register the API by clicking the Setup button or press 'Save Changes'.");
            }
        } else {
            console.log('ERROR', 'Missing varzl (API Seed value)');
        }
    }

})(jQuery);

function randomNumber(min, max) {
    return Math.random() * (max - min) + min;
}


