// -*- Mode: js2; indent-tabs-mode: nil; -*-

$(function() {
    $('#showinfo').bind(
        'click',
        function() {
            $('#showinfo_result_pageurl').text('Loading...');
            $('#showinfo_result_title').text('Loading...');
            $('#showinfo_result_referer').text('Loading...');
            $('#showinfo_result_backlink').text('Loading...');
            $('#showinfo_result_imageurls').text('Loading...');

            if ($('#showinfo_result').css('display') != 'none') {
                $('#showinfo_result').hide();
            }
            $('#showinfo_result').slideDown('slow');

            $.getJSON(
                'api/get', {
                    url: $('#url').val()
                },
                function(data) {
                    if (data.title == undefined) {
                        data.title = 'N/A';
                    }
                    if (data.referer == undefined) {
                        data.referer = 'N/A';
                    }
                    if (data.backlink == undefined) {
                        data.backlink = 'N/A';
                    }
                    if (data.imageurls == undefined) {
                        data.imageurls = ['N/A'];
                    }

                    $('#showinfo_result_pageurl').text(data.pageurl);
                    $('#showinfo_result_title').text(data.title);
                    $('#showinfo_result_referer').text(data.referer);
                    $('#showinfo_result_backlink').text(data.backlink);
                    var imageurls = '';
                    var length = data.imageurls.length;
                    for (var i = 0; i < length; ++i) {
                        imageurls += data.imageurls[i] + "\n";
                    }
                    $('#showinfo_result_imageurls').text(imageurls);
                }
            );
        }
    );
});
