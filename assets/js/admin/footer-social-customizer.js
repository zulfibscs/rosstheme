( function( $ ) {
    'use strict';
    // Minimalized and safe version for WP admin
    function parseItems( raw ) {
        try { var d = JSON.parse( raw ); if ( Array.isArray( d ) ) return d; } catch ( e ) {}
        return [];
    }
    function updateSettingFromItems( $wrap, items ) {
        var raw = JSON.stringify( items );
        $wrap.find( 'input[type="hidden"]' ).val( raw ).trigger( 'change' );
        try { var settingId = $wrap.data( 'setting-id' ); if ( settingId && wp.customize ) wp.customize( settingId ).set( raw ); } catch ( e ) {}
    }
    function renderList( $wrap, items ) {
        var $list = $wrap.find( '.rosstheme-repeater-list' ); $list.empty();
        $list.attr('role','list');
        items.forEach( function( item, idx ) {
            var data = {
                id: item.id || ('i' + idx),
                iconLabel: item.icon || '(no icon)',
                urlLabel: item.url || '',
                previewHtml: item.svg_attachment_url ? '<img src="' + item.svg_attachment_url + '"/>' : (item.icon ? '<span class="rosstheme-icon ' + item.icon + '"></span>' : '' )
            };
            var tpl = window.wp && window.wp.template ? window.wp.template('rosstheme-repeater-item') : null;
            var $row = tpl ? $( tpl( data ) ) : $( '<div class="rosstheme-repeater-item" data-id="'+ ( item.id || ( 'i' + idx ) ) +'" role="listitem"></div>' );
            var iconLabel = item.icon || '(no icon)'; var urlLabel = item.url || '';
            $row.append( '<span class="rosstheme-drag-handle" title="Drag to reorder">☰</span>' );
            var previewHtml = '';
            if ( item.svg_attachment_url ) { previewHtml = '<span class="rosstheme-list-preview"><img src="' + item.svg_attachment_url + '" alt=""/></span>'; }
            else if ( item.svg_attachment_id ) { previewHtml = '<span class="rosstheme-list-preview">SVG</span>'; }
            else if ( item.icon ) { previewHtml = '<span class="rosstheme-list-preview"><span class="rosstheme-icon ' + item.icon + '"></span></span>'; }
            else { previewHtml = '<span class="rosstheme-list-preview rosstheme-list-preview-empty"></span>'; }
            $row.append( previewHtml );
            $row.append( '<div class="rosstheme-repeater-item-main"><strong>' + iconLabel + '</strong> — <small>' + urlLabel + '</small></div>' );
            if ( ! tpl ) {
                var $actions = $('<div class="rosstheme-repeater-item-actions"></div>'); $actions.append('<button class="button rosstheme-repeater-edit" aria-label="Edit social item">Edit</button>'); $actions.append(' <button class="button rosstheme-repeater-remove" aria-label="Remove social item">Remove</button>'); $actions.append(' <button class="button rosstheme-repeater-up" aria-label="Move up">▲</button>'); $actions.append(' <button class="button rosstheme-repeater-down" aria-label="Move down">▼</button>'); $row.append( $actions );
            }
            $row.append( $actions ); $list.append( $row );
        } );
        if ( $list.sortable ) {
            $list.sortable({ handle: '.rosstheme-drag-handle', axis: 'y', update: function() {
                var orderedIds = $list.children().map(function(){ return $(this).attr('data-id'); }).get();
                var map = {}; items.forEach(function(it){ map[it.id || ''] = it; });
                var newItems = []; orderedIds.forEach(function(id){ if ( map[id] ) newItems.push( map[id] ); }); items.forEach(function(it){ if ( orderedIds.indexOf(it.id) === -1 ) newItems.push(it); });
                updateSettingFromItems( $wrap, newItems ); renderList( $wrap, newItems );
            } });
        }
        }
        // Ensure keyboard accessibility: set tabindex for each item
        $list.find('.rosstheme-repeater-item').attr('tabindex', '0');
    }
    function openEditor( $wrap, items, index ) {
        var item = items[index] || { id:'', icon:'', url:'', new_tab:false, aria_label:'', tooltip:'', svg_attachment_id:'', svg_attachment_url:'' };
        var tplModal = window.wp && window.wp.template ? window.wp.template('rosstheme-repeater-modal') : null;
        var modalHtml = tplModal ? tplModal( item ) : '<div class="rosstheme-repeater-modal"><div class="rosstheme-repeater-modal-inner"><div class="rosstheme-modal-left"><p><label>Icon (class or name):<br><input class="rosstheme-field-icon widefat" type="text" value="' + (item.icon||'') + '"/></label></p><p><label>URL:<br><input class="rosstheme-field-url widefat" type="text" value="' + (item.url||'') + '"/></label></p><p><label><input class="rosstheme-field-newtab" type="checkbox" ' + ( item.new_tab ? 'checked' : '' ) + '/> Open in new tab</label></p><p><label>ARIA label:<br><input class="rosstheme-field-aria widefat" type="text" value="' + (item.aria_label || '') + '"/></label></p><p><label>Tooltip:<br><input class="rosstheme-field-tooltip widefat" type="text" value="' + (item.tooltip || '') + '"/></label></p><p><button class="button rosstheme-upload-svg">Upload/select SVG</button> <span class="rosstheme-upload-info">' + (item.svg_attachment_id || '') + '</span></p></div><div class="rosstheme-modal-right"><div class="rosstheme-preview"></div></div></div><p class="rosstheme-modal-actions"><button class="button primary rosstheme-save">Save</button> <button class="button rosstheme-cancel">Cancel</button></p></div>';
        var $modal = $( modalHtml );
        $( 'body' ).append( $modal ); $modal.dialog({ modal:true, title:'Edit Social Icon', width:540, close:function(){ $modal.dialog('destroy').remove(); } });
        var frame; $modal.on('click', '.rosstheme-upload-svg', function(e){ e.preventDefault(); if (frame) { frame.open(); return; } frame = wp.media({ title: 'Select or Upload SVG', library: { type: 'image/svg+xml' }, button: { text: 'Select' }, multiple: false }); frame.on('select', function(){ var attachment = frame.state().get('selection').first().toJSON(); $modal.find('.rosstheme-upload-info').text( attachment.id ); $modal.data('svg_attachment_id', attachment.id ); $modal.data('svg_attachment_url', attachment.url ); $modal.find('.rosstheme-preview').html('<img src="' + attachment.url + '" alt="" style="max-width:100%;height:auto;display:block;"/>'); }); frame.open(); });
        $modal.on('click', '.rosstheme-save', function(e){ e.preventDefault(); var updated = { id: item.id || 'i' + Date.now(), icon: $modal.find('.rosstheme-field-icon').val(), url: $modal.find('.rosstheme-field-url').val(), new_tab: !!$modal.find('.rosstheme-field-newtab').prop('checked'), aria_label: $modal.find('.rosstheme-field-aria').val(), tooltip: $modal.find('.rosstheme-field-tooltip').val(), svg_attachment_id: $modal.data('svg_attachment_id') || item.svg_attachment_id || '', svg_attachment_url: $modal.data('svg_attachment_url') || item.svg_attachment_url || '' }; items[index] = updated; $modal.dialog('close'); updateSettingFromItems( $wrap, items ); renderList( $wrap, items ); });
        $modal.on('click', '.rosstheme-cancel', function(e){ e.preventDefault(); $modal.dialog( 'close' ); });
        $modal.on('input', '.rosstheme-field-icon', function(){ var val = $(this).val(); if ( val ) { $modal.find('.rosstheme-preview').html('<span class="rosstheme-preview-icon ' + val + '" aria-hidden="true"></span>'); } else { $modal.find('.rosstheme-preview').empty(); } });
        if ( item.svg_attachment_id && item.svg_attachment_url ) $modal.find('.rosstheme-preview').html('<img src="' + item.svg_attachment_url + '" alt="" style="max-width:100%;height:auto;display:block;"/>'); else if ( item.icon ) $modal.find('.rosstheme-preview').html('<span class="rosstheme-preview-icon ' + item.icon + '" aria-hidden="true"></span>');
    }
    $( function() {
        $( document ).on( 'click', '.rosstheme-repeater-add', function(e){ e.preventDefault(); var $wrap = $( this ).closest( '.rosstheme-repeater-wrap' ); var items = parseItems( $wrap.find('input[type="hidden"]').val() ); items.push({ id: 'i' + Date.now(), icon:'', url:'', new_tab:false, aria_label:'', tooltip:'', svg_attachment_id:'', svg_attachment_url:'' }); updateSettingFromItems( $wrap, items ); renderList( $wrap, items ); });
        $( document ).on( 'click', '.rosstheme-repeater-remove', function(e){ e.preventDefault(); var $row = $( this ).closest( '.rosstheme-repeater-item' ); var idx = $row.index(); var $wrap = $row.closest( '.rosstheme-repeater-wrap' ); var items = parseItems( $wrap.find( 'input[type="hidden"]' ).val() ); items.splice(idx,1); updateSettingFromItems( $wrap, items ); renderList( $wrap, items ); });
        $( document ).on( 'click', '.rosstheme-repeater-edit', function(e){ e.preventDefault(); var $row = $( this ).closest( '.rosstheme-repeater-item' ); var idx = $row.index(); var $wrap = $row.closest( '.rosstheme-repeater-wrap' ); var items = parseItems( $wrap.find( 'input[type="hidden"]' ).val() ); openEditor( $wrap, items, idx ); });
        // Move up/down buttons
        $( document ).on( 'click', '.rosstheme-repeater-up', function(e){ e.preventDefault(); var $row = $( this ).closest( '.rosstheme-repeater-item' ); var idx = $row.index(); if ( idx <= 0 ) return; var $wrap = $row.closest('.rosstheme-repeater-wrap'); var items = parseItems( $wrap.find('input[type="hidden"]').val() ); var item = items.splice(idx,1)[0]; items.splice(idx-1,0,item); updateSettingFromItems( $wrap, items ); renderList( $wrap, items ); $wrap.find('.rosstheme-repeater-list').children().eq(idx-1).focus(); });
        $( document ).on( 'click', '.rosstheme-repeater-down', function(e){ e.preventDefault(); var $row = $( this ).closest( '.rosstheme-repeater-item' ); var idx = $row.index(); var $wrap = $row.closest('.rosstheme-repeater-wrap'); var items = parseItems( $wrap.find('input[type="hidden"]').val() ); if ( idx >= items.length - 1 ) return; var item = items.splice(idx,1)[0]; items.splice(idx+1,0,item); updateSettingFromItems( $wrap, items ); renderList( $wrap, items ); $wrap.find('.rosstheme-repeater-list').children().eq(idx+1).focus(); });
        $( '.rosstheme-repeater-wrap' ).each( function() { var $wrap = $( this ); var items = parseItems( $wrap.find('input[type="hidden"]').val() ); renderList( $wrap, items ); } );

        // Keyboard handlers for accessibility
        $( document ).on( 'keydown', '.rosstheme-drag-handle', function(e){ var $h = $(this); var $row = $h.closest('.rosstheme-repeater-item'); var $wrap = $row.closest('.rosstheme-repeater-wrap'); var items = parseItems( $wrap.find('input[type="hidden"]').val() ); var idx = $row.index(); switch ( e.key ) { case 'Enter': case ' ': // open editor
                openEditor( $wrap, items, idx ); e.preventDefault(); break; case 'ArrowUp': if ( idx > 0 ) { var item = items.splice(idx,1)[0]; items.splice(idx-1,0,item); updateSettingFromItems( $wrap, items ); renderList( $wrap, items ); $wrap.find('.rosstheme-repeater-list').children().eq(idx-1).find('.rosstheme-drag-handle').focus(); } e.preventDefault(); break; case 'ArrowDown': if ( idx < items.length - 1 ) { var item = items.splice(idx,1)[0]; items.splice(idx+1,0,item); updateSettingFromItems( $wrap, items ); renderList( $wrap, items ); $wrap.find('.rosstheme-repeater-list').children().eq(idx+1).find('.rosstheme-drag-handle').focus(); } e.preventDefault(); break; case 'Delete': case 'Backspace': items.splice(idx,1); updateSettingFromItems( $wrap, items ); renderList( $wrap, items ); e.preventDefault(); break; } });
    } );
} )( jQuery );
