if(!window.vc)var vc={};!function($){var ListenerHelper=vc.events={};_.extend(ListenerHelper,Backbone.Events),ListenerHelper.triggerShortcodeEvents=function(eventType,shortcodeModel){var shortcodeTag;shortcodeTag=shortcodeModel.get("shortcode"),this.trigger("shortcodes",shortcodeModel,eventType),this.trigger("shortcodes:"+shortcodeTag,shortcodeModel,eventType),this.trigger("shortcodes:"+eventType,shortcodeModel),this.trigger("shortcodes:"+shortcodeTag+":"+eventType,shortcodeModel),this.trigger("shortcodes:"+shortcodeTag+":"+eventType+":parent:"+shortcodeModel.get("parent_id"),shortcodeModel),this.triggerParamsEvents(eventType,shortcodeModel)},ListenerHelper.triggerParamsEvents=function(eventType,shortcodeModel){var shortcodeTag,params,settings;shortcodeTag=shortcodeModel.get("shortcode"),params=_.extend({},shortcodeModel.get("params")),settings=vc.map[shortcodeTag],_.isArray(settings.params)&&_.each(settings.params,function(paramSettings){this.trigger("shortcodes:"+eventType+":param",shortcodeModel,params[paramSettings.param_name],paramSettings),this.trigger("shortcodes:"+shortcodeTag+":"+eventType+":param",shortcodeModel,params[paramSettings.param_name],paramSettings),this.trigger("shortcodes:"+eventType+":param:type:"+paramSettings.type,shortcodeModel,params[paramSettings.param_name],paramSettings),this.trigger("shortcodes:"+shortcodeTag+":"+eventType+":param:type:"+paramSettings.type,shortcodeModel,params[paramSettings.param_name],paramSettings),this.trigger("shortcodes:"+eventType+":param:name:"+paramSettings.param_name,shortcodeModel,params[paramSettings.param_name],paramSettings),this.trigger("shortcodes:"+shortcodeTag+":"+eventType+":param:name:"+paramSettings.param_name,shortcodeModel,params[paramSettings.param_name],paramSettings)},this)}}(window.jQuery),function($){vc.AccessPolicyConstructor=function(){this.accessPolicy={},vc.events.trigger("vc:access:initialize",this)},vc.AccessPolicyConstructor.prototype={accessPolicy:{},add:function(part,grant){grant=_.isUndefined(grant)?!0:!!grant,this.accessPolicy[part]=grant},can:function(part){return!!this.accessPolicy[part]}},$(function(){vc.accessPolicy=new vc.AccessPolicyConstructor})}(window.jQuery),function($){vc.events.on("vc:access:initialize",function(access){access.add("be_editor",vc_user_access().editor("backend_editor")),access.add("fe_editor",window.vc_frontend_enabled&&vc_user_access().editor("frontend_editor")),access.add("classic_editor",!vc_user_access().check("backend_editor","disabled_ce_editor",void 0,!0)),!window.vc.gridItemEditor&&vc.events.trigger("vc:access:backend:ready",access)}),vc.events.on("vc:access:backend:ready",function(access){var $switchButton,$buttonsContainer,front,back;if(front="",back="",access.can("fe_editor")&&(front='<span class="vc_spacer"></span><a class="wpb_switch-to-front-composer" href="'+$("#wpb-edit-inline").attr("href")+'">'+window.i18nLocale.main_button_title_frontend_editor+"</a>"),access.can("classic_editor")?access.can("be_editor")&&(back='<span class="vc_spacer"></span><a class="wpb_switch-to-composer" href="javascript:;">'+window.i18nLocale.main_button_title_backend_editor+"</a>"):($("#postdivrich").hide(),access.can("be_editor")&&_.defer(function(){vc.events.trigger("vc:backend_editor:show")})),front||back){var $titleDiv=$("div#titlediv");$buttonsContainer=$titleDiv.length?$('<div class="composer-switch"><span class="logo-icon"></span>'+back+front+"</div>").insertAfter($titleDiv):$('<div class="composer-switch"><span class="logo-icon"></span>'+back+front+"</div>").prependTo("#post-body-content"),access.can("classic_editor")&&($switchButton=$buttonsContainer.find(".wpb_switch-to-composer"),$switchButton.click(function(e){vc.events.trigger("vc:backend_editor:switch")}))}})}(window.jQuery);