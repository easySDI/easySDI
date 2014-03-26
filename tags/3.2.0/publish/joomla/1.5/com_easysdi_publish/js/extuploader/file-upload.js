/*!
 * Ext JS Library 3.3.1
 * Copyright(c) 2006-2010 Sencha Inc.
 * licensing@sencha.com
 * http://www.sencha.com/license
 */

Ext.onReady(function(){

    Ext.QuickTips.init();
    
    var fp = new Ext.FormPanel({
        renderTo: 'fi-form',
        fileUpload: true,
       // width: 500,
        frame: false,
	border: false,
        //title: 'File Upload Form',
        autoHeight: true,
        bodyStyle: 'padding: 10px 10px 0 10px;',
        labelWidth: 50,
        defaults: {
            anchor: '95%',
            allowBlank: false,
            msgTarget: 'side'
        },
        items: [{
            xtype: 'fileuploadfield',
            id: 'form-file',
            emptyText: EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_SELECT_FILE'),
            fieldLabel: EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_FILE'),
            name: 'Filedata',
            buttonText: '',
            buttonCfg: {
                iconCls: 'upload-icon'
            },
	    listeners: {
                'fileselected': function(fb, v){
                    fp.getForm().submit({
	                    url: 'components/com_easysdi_publish/core/script.php',
	                    waitMsg: 'Uploading your files...',
	                    success: function(fp, o){
				if(o.result.success == "true"){
					$('fileList').value = o.result.src;
					searchds_click();
				}else{
					$('errorMsg').style.display = 'block';
					$('errorMsg').style.visibilty = 'visible';
					$('errorMsgCode').innerHTML = excArray[o.result.error];
					$('errorMsgDescr').innerHTML = "";
				}
	                    }
	                });	    
                }
            }
        }]
	/*,
        buttons: [{
            text: 'Save',
            handler: function(){
                if(fp.getForm().isValid()){
	                
                }
            }
        },{
            text: 'Reset',
            handler: function(){
                fp.getForm().reset();
            }
        }]
	*/
    });
    
    

});