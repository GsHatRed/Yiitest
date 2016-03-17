/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/**
 * @fileOverview Image plugin
 */

(function() {

    CKEDITOR.plugins.add( 'image', {
        requires: 'dialog',
        lang: 'af,ar,bg,bn,bs,ca,cs,cy,da,de,el,en,en-au,en-ca,en-gb,eo,es,et,eu,fa,fi,fo,fr,fr-ca,gl,gu,he,hi,hr,hu,id,is,it,ja,ka,km,ko,ku,lt,lv,mk,mn,ms,nb,nl,no,pl,pt,pt-br,ro,ru,si,sk,sl,sq,sr,sr-latn,sv,th,tr,ug,uk,vi,zh,zh-cn', // %REMOVE_LINE_CORE%
        icons: 'image', // %REMOVE_LINE_CORE%
        hidpi: true, // %REMOVE_LINE_CORE%
        init: function( editor ) {
            // Abort when Image2 is to be loaded since both plugins
            // share the same button, command, etc. names (#11222).
            if ( editor.plugins.image2 )
                return;

            var pluginName = 'image';

            // Register the dialog.
            CKEDITOR.dialog.add( pluginName, this.path + 'dialogs/image.js' );

            var allowed = 'img[alt,!src]{border-style,border-width,float,height,margin,margin-bottom,margin-left,margin-right,margin-top,width}',
            required = 'img[alt,src]';

            if ( CKEDITOR.dialog.isTabEnabled( editor, pluginName, 'advanced' ) )
                allowed = 'img[alt,dir,id,lang,longdesc,!src,title]{*}(*)';

            // Register the command.
            editor.addCommand( pluginName, new CKEDITOR.dialogCommand( pluginName, {
                allowedContent: allowed,
                requiredContent: required,
                contentTransformations: [
                [ 'img{width}: sizeToStyle', 'img[width]: sizeToAttribute' ],
                [ 'img{float}: alignmentToStyle', 'img[align]: alignmentToAttribute' ]
                ]
            } ) );

            // Register the toolbar button.
            editor.ui.addButton && editor.ui.addButton( 'Image', {
                label: editor.lang.common.image,
                command: pluginName,
                toolbar: 'insert,10'
            });

            editor.on( 'doubleclick', function( evt ) {
                var element = evt.data.element;
                var sclass = element.getAttribute( 'class');
                if( element.is( 'img') ) {
                    if(sclass == 'listview') {
                        evt.data.dialog = 'listview';
                    }else if(sclass == 'user' || sclass == 'dept'){
                        evt.data.dialog = 'suseranddept';
                    }else if(sclass == 'macro') {
                        evt.data.dialog = 'macro';
                    } else if(sclass == 'tcount'){
                        evt.data.dialog = 'tcount';
                    }else if(sclass == 'signature') {
                        evt.data.dialog = 'signature';
                    }else if(sclass == 'writesign') {
                        evt.data.dialog = 'writesign';
                    }else if(sclass == 'dimensionalcode') {
                        evt.data.dialog = 'dimensionalcode';
                    }else if(sclass == 'countersign'){
                        evt.data.dialog = 'countersign';
                    }else if(sclass == 'fileupload'){
                        evt.data.dialog = 'fileupload';
                    }else if(sclass == 'syscode'){
                        evt.data.dialog = 'syscode';
                    }else if(sclass == 'flowcomment') {
                        evt.data.dialog = 'flowcomment';
                    }else if(!element.data( 'cke-realelement' ) && !element.isReadOnly()) {
                        evt.data.dialog = 'image';
                    }
                }
            });

            // If the "menu" plugin is loaded, register the menu items.
            if ( editor.addMenuItems ) {
                editor.addMenuItems({
                    image: {
                        label: editor.lang.image.menu,
                        command: 'image',
                        group: 'image'
                    }
                });
            }

            // If the "contextmenu" plugin is loaded, register the listeners.
            if ( editor.contextMenu ) {
                editor.contextMenu.addListener( function( element, selection ) {
                    var sclass = element.hasAttribute( 'class' ) ? element.getAttribute( 'class' ) : '';
                    if( sclass == 'user' || sclass == 'dept') {
                        return {
                            suseranddept:CKEDITOR.TRISTATE_OFF
                        }
                    }
                    if( sclass == 'listview' )
                        return {
                            listview: CKEDITOR.TRISTATE_OFF
                        };
                    if( sclass == 'macro' )
                        return {
                            macro: CKEDITOR.TRISTATE_OFF
                        };
                    if( sclass == 'tcount')
                        return {
                            tcount: CKEDITOR.TRISTATE_OFF
                        };
                    if( sclass == 'signature')
                        return {
                            signature: CKEDITOR.TRISTATE_OFF
                        };
                    if( sclass == 'writesign')
                        return {
                            writesign: CKEDITOR.TRISTATE_OFF
                        };
                    if( sclass == 'dimensionalcode')
                        return {
                            dimensionalcode: CKEDITOR.TRISTATE_OFF
                        };
                    if( sclass == 'countersign')
                        return {
                            countersign: CKEDITOR.TRISTATE_OFF
                        };
                    if( sclass == 'fileupload')
                        return {
                            fileupload: CKEDITOR.TRISTATE_OFF
                        };
                    if( sclass == 'syscode')
                        return {
                            syscode: CKEDITOR.TRISTATE_OFF
                        };
                    if( sclass == 'flowcomment')
                        return {
                            flowcomment: CKEDITOR.TRISTATE_OFF
                        };
                    if ( getSelectedImage( editor, element ) )
                        return {
                            image: CKEDITOR.TRISTATE_OFF
                        };
                });
            }
        },
        afterInit: function( editor ) {
            // Abort when Image2 is to be loaded since both plugins
            // share the same button, command, etc. names (#11222).
            if ( editor.plugins.image2 )
                return;

            // Customize the behavior of the alignment commands. (#7430)
            setupAlignCommand( 'left' );
            setupAlignCommand( 'right' );
            setupAlignCommand( 'center' );
            setupAlignCommand( 'block' );

            function setupAlignCommand( value ) {
                var command = editor.getCommand( 'justify' + value );
                if ( command ) {
                    if ( value == 'left' || value == 'right' ) {
                        command.on( 'exec', function( evt ) {
                            var img = getSelectedImage( editor ),
                            align;
                            if ( img ) {
                                align = getImageAlignment( img );
                                if ( align == value ) {
                                    img.removeStyle( 'float' );

                                    // Remove "align" attribute when necessary.
                                    if ( value == getImageAlignment( img ) )
                                        img.removeAttribute( 'align' );
                                } else
                                    img.setStyle( 'float', value );

                                evt.cancel();
                            }
                        });
                    }

                    command.on( 'refresh', function( evt ) {
                        var img = getSelectedImage( editor ),
                        align;
                        if ( img ) {
                            align = getImageAlignment( img );

                            this.setState(
                                ( align == value ) ? CKEDITOR.TRISTATE_ON : ( value == 'right' || value == 'left' ) ? CKEDITOR.TRISTATE_OFF : CKEDITOR.TRISTATE_DISABLED );

                            evt.cancel();
                        }
                    });
                }
            }
        }
    });

    function getSelectedImage( editor, element ) {
        if ( !element ) {
            var sel = editor.getSelection();
            element = sel.getSelectedElement();
        }

        if ( element && element.is( 'img' ) && !element.data( 'cke-realelement' ) && !element.isReadOnly() )
            return element;
    }

    function getImageAlignment( element ) {
        var align = element.getStyle( 'float' );

        if ( align == 'inherit' || align == 'none' )
            align = 0;

        if ( !align )
            align = element.getAttribute( 'align' );

        return align;
    }

})();

/**
 * Whether to remove links when emptying the link URL field in the image dialog.
 *
 *		config.image_removeLinkByEmptyURL = false;
 *
 * @cfg {Boolean} [image_removeLinkByEmptyURL=true]
 * @member CKEDITOR.config
 */
CKEDITOR.config.image_removeLinkByEmptyURL = true;

/**
 * Padding text to set off the image in preview area.
 *
 *		config.image_previewText = CKEDITOR.tools.repeat( '___ ', 100 );
 *
 * @cfg {String} [image_previewText='Lorem ipsum dolor...' (placeholder text)]
 * @member CKEDITOR.config
 */
