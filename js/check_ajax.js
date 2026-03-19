$(document).ready(function () {
    $("#email").change(function () {
        var email = $(this).val();
        if (email.length > 8) {
            $("#available").html(
                '<span class="check">checking availability...</span>',
            );

            $.ajax({
                type: "get",
                url: "check.php",
                data: "email=" + email,
                success: function (response) {
                    if (response == "YES") {
                        $("#available").html(
                            '<span class="avai">Email is available.</span>',
                        );
                    } else if (response == "NO") {
                        $("#available").html(
                            '<span class="avai">Email NOT available.</span>',
                        );
                    }
                },
            });
        } else {
            $("#available").html(
                '<span class="short">Email is too short.</span>',
            );
        }
    });
});
