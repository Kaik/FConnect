{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="view" size="small"}
    <h3>{gt text="Settings"}</h3>
</div>

    <p class="z-warningmsg">{gt text='To get facebook api keys you need to create app on Facebook'}</p>
    <form id="fconnect_config" class="z-form" action="{modurl modname="fconnect" type="admin" func="updateconfig"}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
            <fieldset>
                <legend>{gt text="Secrets"}</legend>
                <div class="z-formrow">
                    <label for="fconnect_appid">{gt text="App ID"}</label>
                    <input id="fconnect_appid" name="appid" type="text" value="{$modvars.FConnect.appid}" />
                </div>
                <div class="z-formrow">
                    <label for="fconnect_secretkey">{gt text="App Secret:"}</label>
                    <input id="fconnect_secretkey" name="secretkey" type="text" value="{$modvars.FConnect.secretkey}" />
                </div>
            </fieldset>
            <div class="z-formbuttons z-buttons">
                {button src='button_ok.png' set='icons/extrasmall' __alt='Save' __title='Save' __text='Save'}
                <a href="{modurl modname='fconnect' type='admin' func='main'}" title="{gt text='Cancel'}">{img modname='core' src='button_cancel.png' set='icons/extrasmall' __alt='Cancel' __title='Cancel'} {gt text='Cancel'}</a>
            </div>
        </div>
    </form>
{adminfooter}