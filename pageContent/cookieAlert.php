<form id="cookies-form">
    <div class="alert alert-primary" role="alert" style="position: fixed; bottom: 0; left: 0; width: 100%; margin-bottom: 0;">
        <div style="position: relative;">
            <span style="width: 20%;  float:left; max-width:100px;"><button type="submit" name="agree" class="btn btn-warning btn-rnd">Agree</button></span>
            <p style="width: 75%;">You need to give your consent as this website uses cookies.</p>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $("#cookies-form").on("submit", function() {
            e.preventDefault();
            var d = new Date();
            d.setTime(d.getTime() + (24 * 60 * 60 * 1000));
            var expires = "expires=" + d.toUTCString();
            document.cookie =  "CookiesAccepted=yes;" + expires + ";path=/";

        })
    });
</script>