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
        <form method="post" action="addonmodules.php?module=mailerlite&action=synchronizedlist" id="frmMailerlite">
            <div class="form-group">
                <label for="inputPrimaryList">Primary Subscription List</label>
                {{select}}
                <p class="help-block">Choose the primary list you wish to subscribe users to. E-commerce integration will be configured for use with this list. We recommend creating a new list.</p>
            </div>
            <div>
                {{subscriptionbtn}}
            </div>
        </form>
    </div>
</div>
