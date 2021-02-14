window.addEventListener('load', function () {
    $("document").ready(function () {
        $('#sidebarToggle').on('click', function () {
            $("#sidebar-container").toggleClass("open shadow");
        });

        $(document).on('click', '.group-dropdown a', function (event) {
            $("#sidebar-container").removeClass("open shadow");
        });

        $(document).on('click', '#home-menu', function (event) {
            $("#sidebar-container").removeClass("open shadow");
        });

        // loads select2
        var script = document.createElement("script");
        script.src = "//cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js";
        script.type = "text/javascript";
        document.getElementsByTagName("head")[0].appendChild(script);


        // encrypted checkbox formalization
        var script = document.createElement("script");
        script.src = "//code.jquery.com/ui/1.12.1/jquery-ui.js";
        script.type = "text/javascript";
        document.getElementsByTagName("head")[0].appendChild(script);

        $(document).on("click", ".removeCredRow", function () {
            $(this).closest('.credential-item').remove();
        });

        $(document).on("change", ".changeIsEncrypted", function () {
            if ($(this).is(":checked")) {
                $(this).prev().attr('disabled', true);
            } else {
                $(this).prev().attr('disabled', false);
            }
        });
    });
}, false);