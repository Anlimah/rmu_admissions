<?php
session_start();
if (!isset($_SESSION["_step6Token"])) {
    $rstrong = true;
    $_SESSION["_step6Token"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchases</title>

</head>

<body>
    <img src="../images/RMU-LOG.png" alt="RMU LOG">
    <h1>Step 6</h1>
    <form action="#" id="step1Form" method="post" enctype="multipart/form-data">
        <div>
            <label for="gender">Application type</label>
            <select name="app_type" id="app_type">
                <option value="Degree" selected>Degree/diploma</option>
                <option value="Masters">Masters</option>
                <option value="Short">Short courses</option>
            </select>
        </div>
        <div>
            <label for="gender">Payment Method</label>
            <select name="app_method" id="app_method">
                <option value="Momo" selected>Mobile Money</option>
                <option value="Bank">Bank</option>
            </select>
        </div>
        <button type="submit">Continue</button>
        <input type="hidden" name="_v6Token" value="<?php echo $_SESSION["_step6Token"]; ?>">
    </form>

    <script src="../js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#step1Form").on("submit", function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "../api/verifyStep6",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result) {
                            if ($("#app_method").val() == "Bank") {
                                window.location.href = "purchase_step7_bank.php";
                            } else if ($("#app_method").val() == "Momo") {
                                window.location.href = "purchase_step7_momo.php";
                            }
                        }
                    },
                    error: function(error) {}
                });
            });
        });
    </script>
</body>

</html>