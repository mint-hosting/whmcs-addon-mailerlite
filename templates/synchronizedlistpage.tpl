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
    .mailerlite-content > .nav {
        margin: 30px 0 0 0;
        padding: 20px 0 0 0;
        border-top: 1px solid #ddd;
    }
</style>

<div class="mailerlite">
    <div class="mailerlite-content">
        <a href="{{modulelink}}"><img src="../modules/addons/mailerlite/logo.png" rel="MailerLite" class="logo"></a>
        {{errormessage}}
        <p>Selected Subscription list: <strong>{{selectedlist}}</strong></p>

        <br>

        <p>Sync Status: <span class="label label-success" style="margin-left:8px;padding:5px 10px;font-size:1em;text-transform:initial;">All up-to-date</span></p>

        <br>

        <p>To setup automations or start a new campaign, visit <a href="https://app.mailerlite.com/users/login/" target="_blank">www.mailerlite.com</a></p>
        <div class="nav">
            <a href="addonmodules.php?module=mailerlite&action=disconnect&list={{selectedlistid}}" class="btn btn-default">
                Disconnect Integration
            </a>
        </div>
    </div>
</div>
