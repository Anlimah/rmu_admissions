<?php
require_once('../../bootstrap.php');

use Src\Controller\UsersController;

$user = new UsersController();
$personal = $user->fetchApplicantPersI($user_id);
$appStatus = $user->getApplicationStatus($user_id);
$fullname = !empty($personal[0]["middle_name"]) ? $personal[0]["first_name"] . " " . $personal[0]["middle_name"] . " " . $personal[0]["last_name"] : $personal[0]["first_name"] . " " . $personal[0]["last_name"];
?>

<fieldset class="fieldset">
    <div style="width: 100%; border: 1px dashed red;padding: 20px; background-color:#ffffcc;">
        <div>
            <label for="" style="color: red;">IMPORTANT</label>
        </div>
        <p>
            AN APPLICANT WHO MAKES A FALSE STATEMENT OR WITHHOLDS RELEVANT INFORMATION MAY BE REFUSED ADMISSION. IF HE HAS ALREADY COME INTO THE UNIVERSITY, HE WILL BE WITHDRAWN.
        </p>
        <div style="width: 100%; background-color: #036; color: #fff; font-size: smaller; padding: 5px 10px">
            <b>DECLARATION</b>
        </div>
        <div style="align-items:center; margin-top: 10px">
            <p style="margin-left: 10px;">I <label for=""><?= $fullname ?></label>, certify that the information provided above is valid and will be held personally responsible for its authenticity and will bear any consequences for any invalid information provided.</p>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="accept-declared" required>
                <label class="form-check-label" for="accept-declared" style="margin-left: 10px"> Agree</label>
                <div class="invalid-feedback">
                    You must agree before submitting.
                </div>
            </div>

            <div style="width: 100%; text-align:center">
                <input type="submit" class="btn btn-primary" value="Complete Application" style="margin-top: 20px !important; background-color: #036; padding: 15px 20px !important">
            </div>
        </div>
    </div>
</fieldset>