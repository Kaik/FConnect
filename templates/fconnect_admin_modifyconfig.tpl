{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="view" size="small"}
    <h3>{gt text="Settings"}</h3>
</div>
<div class="z-floatleft" style="width:63%;">
    <form id="fconnect_config" class="z-form" action="{modurl modname="fconnect" type="admin" func="updateconfig"}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />           
            
            <fieldset>
                <legend>{gt text="Connection settings"}</legend>
                    {if $modvars.FConnect.appid eq '' || $modvars.FConnect.secretkey eq ''}
                    <p class="z-warningmsg">{gt text='To get facebook api keys you need to create app on Facebook'}</p>
                    {/if}
                <div class="z-formrow">
                    <label for="fconnect_appid">{gt text="App ID"}</label>
                    <input id="fconnect_appid" name="appid" type="text" value="{$modvars.FConnect.appid}" />
                </div>
                <div class="z-formrow">
                    <label for="fconnect_secretkey">{gt text="App Secret:"}</label>
                    <input id="fconnect_secretkey" name="secretkey" type="text" value="{$modvars.FConnect.secretkey}" />
                </div>
               <div class="z-formrow">
               <label for="fconnect_isenabled">{gt text="Enable authentication"}</label>
               <input {if $modvars.FConnect.appid eq '' || $modvars.FConnect.secretkey eq ''}disabled="disabled"{/if}id="fconnect_isenabled" name="isenabled" type="checkbox" value="1" {if $modvars.FConnect.isenabled}checked="checked"{/if} /> 
               {if $modvars.FConnect.appid eq '' || $modvars.FConnect.secretkey eq ''}
               <p class="z-warningmsg">{gt text='No connection settings detected. Athentication is disabled.'}</p>
               {/if}
               {if $modvars.FConnect.isenabled}
                <p class="z-informationmsg">{gt text='Notice: Facebook authentication is enabled.'}</p>               
               {else}
                <p class="z-informationmsg">{gt text='Notice: Facebook authentication is not enabled. Buttons to log in are not shown.'}</p>              
               {/if}               
               </div>
            </fieldset>
            
            <fieldset>
            <legend>{gt text="Enable javascript support"}</legend>
               <label for="fconnect_jsisenabled">{gt text="Enable"}</label>
               <input id="fconnect_jsisenabled" name="jsisenabled" type="checkbox" disabled="disabled" value="1" {if $modvars.FConnect.jsisenabled}checked="checked"{/if} /> 
                {if $modvars.FConnect.jsisenabled}
                <p class="z-informationmsg">{gt text='Notice: Yes'}</p>
                {else}
                <p class="z-warningmsg">{gt text='Notice: Not supported yet'}</p>
                {/if}
            </fieldset>
            
            <fieldset>
            <legend>{gt text="User informations"}</legend>
               <label for="fconnect_userdataisenabled">{gt text="Enable"}</label>
               <input id="fconnect_userdataisenabled" name="userdataisenabled" disabled="disabled" type="checkbox" value="1" {if $modvars.FConnect.userdataisenabled}checked="checked"{/if} />    
                 {if $modvars.FConnect.userdataisenabled}
                <p class="z-informationmsg">{gt text='Notice:  Yes'}</p>
                {foreach from=$properties item=p}
                <p>{$p.prop_attribute_name}({$p.prop_label}) </p>
                {/foreach}                
                {else}
                <p class="z-warningmsg">{gt text='Notice: Not supported yet'}</p>
                {/if}                                        
            </fieldset> 
            
            <fieldset>
             <legend>{gt text="Page content creation"}</legend>
               <label for="fconnect_contentisenabled">{gt text="Enable"}</label>
               <input id="fconnect_contentisenabled" name="contentisenabled" type="checkbox" value="1" {if $modvars.FConnect.contentisenabled}checked="checked"{/if} />    
               {if $modvars.FConnect.contentisenabled}
               
                    {if $fb.pageperms}
                        <div class="z-statusmsg z-clearfix z-sub">{gt text='publish_stream, manage_pages permissions granted '}
                        </div> 
                    {else}
                         <div class="z-warningmsg z-clearfix z-sub">
                        <strong>{gt text='Notice: To use this you need to be facebook page admin and publish_stream, manage_pages permissions must be granted'}</strong> </br>    
                        <a href="{$fb.getpermsurl}">{gt text="Click here get permissions"}</a>
                        </div> 
                    {/if}
                    <p class="z-informationmsg">{gt text='Notice: Extension is active.'}
                        <a href="{modurl modname='fconnect' type='admin' func='content'}" title="{gt text='Page content creation settings'}">{img modname='core' src='editcopy.png' set='icons/extrasmall' __alt='Page content creation settings' __title='Page content creation settings'} {gt text='Page content creation settings'}</a>
                    </p>
               {/if}                        
            </fieldset>   
            
            <div class="z-formbuttons z-buttons">
                {button src='button_ok.png' set='icons/extrasmall' __alt='Save' __title='Save' __text='Save'}
                <a href="{modurl modname='fconnect' type='admin' func='main'}" title="{gt text='Cancel'}">{img modname='core' src='button_cancel.png' set='icons/extrasmall' __alt='Cancel' __title='Cancel'} {gt text='Cancel'}</a>
            </div>
        </div>
    </form>
    </div>
<div class="z-floatright" style="width:33%;">
<h3>{gt text="Your connection status"}</h3>

{if $modvars.FConnect.appid eq '' || $modvars.FConnect.secretkey eq ''}    
   
    <p class="z-warningmsg">{gt text='No connection settings detected.'}</p>
    
{else}    

    {if $fb.id neq 0}
        <div class="z-statusmsg z-clearfix z-sub">
        <strong>{gt text="Facebook user"}</strong>    
        <img class="z-floatright" style="width:50px;border:3px solid #fff;" src="https://graph.facebook.com/{$fb.id}/picture">
        <p class="z-sub">{$fb.me.name}</p>
        <p class="z-sub">{$fb.me.id}</p>
        <p class="z-sub">{$fb.me.link}</p>
        <p class="z-sub">{$fb.me.email}</p>
        
        <h4>{gt text="Permissions"}</h4>
        {foreach from=$fb.perms.data.0 key=p item=enabled}
           {$enabled|yesno:'true'} {$p}   
           {/foreach}

        
        </div>
        
       
            {if $fb.zuid}
                <div class="z-statusmsg z-clearfix z-sub">
                <strong>{gt text="Zikula user"}</strong>  
                <p>{usergetvar name='uname' uid=$fb.zuid}</p>
                {useravatar uid=$fb.zuid}
                </div> 
            {else}
                <div class="z-warningmsg z-clearfix z-sub">
                <p><strong>{gt text="No user account is connected with this facebook user"}</strong></p>
                {if $fb.sameemail|@count gt 0}
                <p>{gt text="Found users by facebook email"}</p>                
                {foreach from=$fb.sameemail item=u}
                {$u.uname}  {$u.uid} 
                {/foreach}
                {else}
                <p>{gt text="Users by facebook email not found"}</p> 
                {/if}
                </div>           
            {/if}

    {else}
        <div class="z-warningmsg z-clearfix z-sub">
        <strong>{gt text="No facebook user connected."}</strong> </br>    
        <a href="{$fb.loginUrl}">{gt text="Click here to connect"}</a>
        </div>   
    {/if}    
     
{/if}
</div> 
{adminfooter}
