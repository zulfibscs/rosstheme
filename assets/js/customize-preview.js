/* Live preview updates for Footer Social settings */
( function( wp, $ ) {
    'use strict';
    var selector = '.footer-social';
    function escAttr( str ) { if (!str) return ''; return String(str).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
    function renderIconsFromSetting( raw ) {
        var items = [];
        try { items = JSON.parse( raw ); } catch (e) { items = []; }
        var $wrap = $( selector ); if ( ! $wrap.length ) return; $wrap.empty(); var $list = $('<ul class="footer-social-list" style="list-style:none;margin:0;padding:0;display:flex;gap:0.5rem;align-items:center;"></ul>');
        items.forEach( function( item ) { var $li = $('<li class="footer-social-item"></li>'); var iconHtml = ''; var href = item.url ? escAttr( item.url ) : '#'; if ( item.svg_attachment_url ) { iconHtml = '<img src="' + escAttr( item.svg_attachment_url ) + '" alt="" />'; } else if ( item.icon ) { iconHtml = '<span class="social-icon ' + escAttr( item.icon ) + '"></span>'; } else { iconHtml = '<span class="social-icon-placeholder"></span>'; } var attrs = ''; if ( item.new_tab ) attrs += ' target="_blank" rel="noopener noreferrer"'; if ( item.aria_label ) attrs += ' aria-label="' + escAttr( item.aria_label ) + '"'; var $a = $('<a class="social-icon" href="' + href + '"'+ attrs + '>' + iconHtml + '</a>'); $li.append( $a ); $list.append( $li ); } ); $wrap.append( $list );
    }
    wp.customize.bind( 'ready', function() {
        if ( wp.customize( 'footer_social_icons' ) ) {
            wp.customize( 'footer_social_icons', function( value ) { value.bind( function( newval ) { renderIconsFromSetting( newval ); } ); } );
        }
    } );
} )( wp, jQuery );
