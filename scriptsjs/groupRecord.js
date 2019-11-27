 $(document).ready(function() {
            $("#reminder").on("click", function() {
                sendRequest(completed = false);
            });
            $("#completed").on("click", function() {
                sendRequest(completed = true);
            });
            $(".group-option").on("click", function(event){
                event.preventDefault();
                window.location = 'groupRecord.php?group=' + $(this).text().trim();
            });

            function sendRequest(completed = false) {
                let group = parseInt($("#group_number").val());
                let reqData = {
                    group_number: group
                };
                if (!completed) {
                    reqData.reminderGrades = "set";
                    $("#reminder").prop("disabled", true);
                            $("#reminder").css("background-color", "gray");
                            $("#reminder").css("border", "none");
                            $("#reminder").blur();
                    $("#reminder").text("Sending emails. Please wait...");
                } else {
                    reData.completedGrades = "set";
                    $("#completed").prop("disabled", true);
                       $("#completed").css("background-color", "gray");
                       $("#completed").css("border", "none");
                       $("#completed").blur();
                    $("#completed").text("Sending emails. Please wait...");
                }
                $.ajax({
                    url: 'sendMail.php',
                    data: reqData,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(output, status, xhr) {
                        $("#top-alert-success").css("display", "block");
                        if (completed) {
                            $("#completed").text("Evaluation sent");
                            $("#success-message").text("Final grades emailed to group members");
                        } else {
                            $("#reminder").text("Reminder sent");
                            $("#success-message").text("Reminder email sent to group members");
                        }
                        setTimeout(function() {
                            $("#top-alert").css("display", "none");
                        }, 5000);
                    },
                    error: function(xhr, status, error) {
                        $("#top-alert-error").css("display", "block");
                        $("#error-message").text(xhr.responseJSON.message);
                        setTimeout(function() {
                            $("#top-alert-error").css("display", "none");
                        }, 5000);
                        if (completed) {
                            $("#completed").prop("disabled", false);
                       $("#completed").css("background-color", "");
                       $("#completed").css("border", "");
                       $("#completed").blur();
                            $("#completed").text("Send group evaluation");
                        } else {
                            $("#reminder").css("background-color", "");
                       $("#reminder").css("border", "");
                       $("#reminder").blur();
                            $("#reminder").text("Send group reminder");
                        }
                    }
                });
                
            }
        });