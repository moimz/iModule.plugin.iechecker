<?php
/**
 * 이 파일은 iModule IE버전확인플러그인 일부입니다. (https://www.imodules.io)
 *
 * 플러그인 환경설정 패널을 가져온다.
 * 
 * @file /plugins/iechecker/admin/configs.php
 * @author Arzz (arzz@arzz.com)
 * @license GPLv3
 * @version 3.0.0
 * @modified 2018. 6. 21.
 */
if (defined('__IM__') == false) exit;
?>
<script>
new Ext.form.Panel({
	id:"PluginConfigForm",
	border:false,
	bodyPadding:10,
	width:700,
	fieldDefaults:{labelAlign:"right",labelWidth:100,anchor:"100%",allowBlank:false},
	items:[
		new Ext.form.FieldSet({
			title:Plugin.getText("iechecker","admin/configs/form/default_setting"),
			items:[
				Admin.templetField(Plugin.getText("iechecker","admin/configs/form/templet"),"templet","plugin","iechecker",false),
				new Ext.form.ComboBox({
					fieldLabel:Plugin.getText("iechecker","admin/configs/form/minimun"),
					name:"minimum",
					allowBlank:false,
					store:new Ext.data.ArrayStore({
						fields:["display","value"],
						data:[["Internet Explorer 6 or Higher",6],["Internet Explorer 7 or Higher",7],["Internet Explorer 8 or Higher",8],["Internet Explorer 9 or Higher",9],["Internet Explorer 10 or Higher",10],["Internet Explorer 11 or Higher",11]]
					}),
					displayField:"display",
					valueField:"value",
					afterBodyEl:'<div class="x-form-help">'+Plugin.getText("iechecker","admin/configs/form/minimun_help")+'</div>'
				}),
				new Ext.form.Checkbox({
					fieldLabel:Plugin.getText("iechecker","admin/configs/form/logging"),
					name:"logging",
					uncheckedValue:"",
					boxLabel:Plugin.getText("iechecker","admin/configs/form/logging_help")
				})
			]
		})
	]
});
</script>