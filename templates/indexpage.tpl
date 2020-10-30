<style>
    .mailerlite {
        display: flex;
        width: 100%;
        justify-content: center;
        height: 100%;
        align-items: center;
    }

    .mailerlite-content {
        display: flex;
        flex-direction: column;
        padding: 30px 50px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 40%;
    }

    .logo {
        max-width: 100%;
        margin-bottom: 50px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .mailerlite-api-key {
        display: block;
        width: 100%;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    }
</style>

<div class="mailerlite">
    <div class="mailerlite-content">
        <a href="{{modulelink}}"><img src="../modules/addons/mailerlite/logo.png" rel="MailerLite" class="logo"></a>
        {{errormessage}}
        <form method="post" action="addonmodules.php?module=mailerlite&action=validate" id="frmMailerlite">
            <div class="form-group">
                <label for="mailerlite-api-key"><strong>API Integration Key</strong></label>
                <input type="text" name="mailerlite-api-key" id="mailerlite-api-key" class="form-control" placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
            </div>
            <div>
                <button type="submit" class="btn btn-primary">
                    Validate API Key
                </button>
            </div>
        </form>
    </div>
</div>
