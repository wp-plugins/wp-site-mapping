//helper functions
function getAttr(s, n) {
    console.log(n);
    n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
    console.log(n);
    return n ? window.decodeURIComponent(n[1]) : '';
};

function getAttrNoDecode(s, n) {
    console.log(n);
    n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
    console.log(n);
    return n ? n[1] : '';
};

function getAttrNoDecodeNoQuote(s, n) {
    console.log(n);
    n = new RegExp(n + '=([^ ]+)', 'g').exec(s);
    console.log(n);
    return n ? n[1] : '';
};

function html(cls, data, url) {
    var placeholder = url + '/../images/sitemap_placeholder.png';
    data = window.encodeURIComponent(data);

    return '<img src="' + placeholder + '" class="mceItem ' + cls + '" ' + 'data-sh-attr="' + data + '" data-mce-resize="false" data-mce-placeholder="1" />';
}

function replaceShortcodes(content, url) {
    //match [bs3_panel(attr)](con)[/bs3_panel]
    return content.replace(/\[showsitemap([^\]]*)\]/g, function (all, attr) {
        return html('wpsm_panel', attr, url);
    });
}

function restoreShortcodes(content, url) {
    //match any image tag with our class and replace it with the shortcode's content and attributes
    return content.replace(/(?:<p(?: [^>]+)?>)*(<img [^>]+>)(?:<\/p>)*/g, function (match, image) {
        var data = getAttr(image, 'data-sh-attr');

        if (data) {
            return '[showsitemap ' + data + ']';
        }
        return match;
    });
}
function htmlentities(string, quote_style, charset, double_encode) {
    //  discuss at: http://phpjs.org/functions/htmlentities/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    //  revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    //  revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: nobbler
    // improved by: Jack
    // improved by: Rafał Kukawski (http://blog.kukawski.pl)
    // improved by: Dj (http://phpjs.org/functions/htmlentities:425#comment_134018)
    // bugfixed by: Onno Marsman
    // bugfixed by: Brett Zamir (http://brett-zamir.me)
    //    input by: Ratheous
    //  depends on: get_html_translation_table
    //   example 1: htmlentities('Kevin & van Zonneveld');
    //   returns 1: 'Kevin &amp; van Zonneveld'
    //   example 2: htmlentities("foo'bar","ENT_QUOTES");
    //   returns 2: 'foo&#039;bar'

    var hash_map = get_html_translation_table('HTML_ENTITIES', quote_style),
        symbol = '';
    string = string == null ? '' : string + '';

    if (!hash_map) {
        return false;
    }

    if (quote_style && quote_style === 'ENT_QUOTES') {
        hash_map["'"] = '&#039;';
    }

    if (!!double_encode || double_encode == null) {
        for (symbol in hash_map) {
            if (hash_map.hasOwnProperty(symbol)) {
                string = string.split(symbol)
                    .join(hash_map[symbol]);
            }
        }
    } else {
        string = string.replace(/([\s\S]*?)(&(?:#\d+|#x[\da-f]+|[a-zA-Z][\da-z]*);|$)/g, function (ignore, text, entity) {
            for (symbol in hash_map) {
                if (hash_map.hasOwnProperty(symbol)) {
                    text = text.split(symbol)
                        .join(hash_map[symbol]);
                }
            }

            return text + entity;
        });
    }

    return string;
}

function get_html_translation_table(table, quote_style) {
    //  discuss at: http://phpjs.org/functions/get_html_translation_table/
    // original by: Philip Peterson
    //  revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: noname
    // bugfixed by: Alex
    // bugfixed by: Marco
    // bugfixed by: madipta
    // bugfixed by: Brett Zamir (http://brett-zamir.me)
    // bugfixed by: T.Wild
    // improved by: KELAN
    // improved by: Brett Zamir (http://brett-zamir.me)
    //    input by: Frank Forte
    //    input by: Ratheous
    //        note: It has been decided that we're not going to add global
    //        note: dependencies to php.js, meaning the constants are not
    //        note: real constants, but strings instead. Integers are also supported if someone
    //        note: chooses to create the constants themselves.
    //   example 1: get_html_translation_table('HTML_SPECIALCHARS');
    //   returns 1: {'"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;'}

    var entities = {},
        hash_map = {},
        decimal;
    var constMappingTable = {},
        constMappingQuoteStyle = {};
    var useTable = {},
        useQuoteStyle = {};

    // Translate arguments
    constMappingTable[0] = 'HTML_SPECIALCHARS';
    constMappingTable[1] = 'HTML_ENTITIES';
    constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
    constMappingQuoteStyle[2] = 'ENT_COMPAT';
    constMappingQuoteStyle[3] = 'ENT_QUOTES';

    useTable = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
    useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() :
        'ENT_COMPAT';

    if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
        throw new Error('Table: ' + useTable + ' not supported');
        // return false;
    }

    entities['38'] = '&amp;';
    if (useTable === 'HTML_ENTITIES') {
        entities['160'] = '&nbsp;';
        entities['161'] = '&iexcl;';
        entities['162'] = '&cent;';
        entities['163'] = '&pound;';
        entities['164'] = '&curren;';
        entities['165'] = '&yen;';
        entities['166'] = '&brvbar;';
        entities['167'] = '&sect;';
        entities['168'] = '&uml;';
        entities['169'] = '&copy;';
        entities['170'] = '&ordf;';
        entities['171'] = '&laquo;';
        entities['172'] = '&not;';
        entities['173'] = '&shy;';
        entities['174'] = '&reg;';
        entities['175'] = '&macr;';
        entities['176'] = '&deg;';
        entities['177'] = '&plusmn;';
        entities['178'] = '&sup2;';
        entities['179'] = '&sup3;';
        entities['180'] = '&acute;';
        entities['181'] = '&micro;';
        entities['182'] = '&para;';
        entities['183'] = '&middot;';
        entities['184'] = '&cedil;';
        entities['185'] = '&sup1;';
        entities['186'] = '&ordm;';
        entities['187'] = '&raquo;';
        entities['188'] = '&frac14;';
        entities['189'] = '&frac12;';
        entities['190'] = '&frac34;';
        entities['191'] = '&iquest;';
        entities['192'] = '&Agrave;';
        entities['193'] = '&Aacute;';
        entities['194'] = '&Acirc;';
        entities['195'] = '&Atilde;';
        entities['196'] = '&Auml;';
        entities['197'] = '&Aring;';
        entities['198'] = '&AElig;';
        entities['199'] = '&Ccedil;';
        entities['200'] = '&Egrave;';
        entities['201'] = '&Eacute;';
        entities['202'] = '&Ecirc;';
        entities['203'] = '&Euml;';
        entities['204'] = '&Igrave;';
        entities['205'] = '&Iacute;';
        entities['206'] = '&Icirc;';
        entities['207'] = '&Iuml;';
        entities['208'] = '&ETH;';
        entities['209'] = '&Ntilde;';
        entities['210'] = '&Ograve;';
        entities['211'] = '&Oacute;';
        entities['212'] = '&Ocirc;';
        entities['213'] = '&Otilde;';
        entities['214'] = '&Ouml;';
        entities['215'] = '&times;';
        entities['216'] = '&Oslash;';
        entities['217'] = '&Ugrave;';
        entities['218'] = '&Uacute;';
        entities['219'] = '&Ucirc;';
        entities['220'] = '&Uuml;';
        entities['221'] = '&Yacute;';
        entities['222'] = '&THORN;';
        entities['223'] = '&szlig;';
        entities['224'] = '&agrave;';
        entities['225'] = '&aacute;';
        entities['226'] = '&acirc;';
        entities['227'] = '&atilde;';
        entities['228'] = '&auml;';
        entities['229'] = '&aring;';
        entities['230'] = '&aelig;';
        entities['231'] = '&ccedil;';
        entities['232'] = '&egrave;';
        entities['233'] = '&eacute;';
        entities['234'] = '&ecirc;';
        entities['235'] = '&euml;';
        entities['236'] = '&igrave;';
        entities['237'] = '&iacute;';
        entities['238'] = '&icirc;';
        entities['239'] = '&iuml;';
        entities['240'] = '&eth;';
        entities['241'] = '&ntilde;';
        entities['242'] = '&ograve;';
        entities['243'] = '&oacute;';
        entities['244'] = '&ocirc;';
        entities['245'] = '&otilde;';
        entities['246'] = '&ouml;';
        entities['247'] = '&divide;';
        entities['248'] = '&oslash;';
        entities['249'] = '&ugrave;';
        entities['250'] = '&uacute;';
        entities['251'] = '&ucirc;';
        entities['252'] = '&uuml;';
        entities['253'] = '&yacute;';
        entities['254'] = '&thorn;';
        entities['255'] = '&yuml;';
    }

    if (useQuoteStyle !== 'ENT_NOQUOTES') {
        entities['34'] = '&quot;';
    }
    if (useQuoteStyle === 'ENT_QUOTES') {
        entities['39'] = '&#39;';
    }
    entities['60'] = '&lt;';
    entities['62'] = '&gt;';

    // ascii decimals to real symbols
    for (decimal in entities) {
        if (entities.hasOwnProperty(decimal)) {
            hash_map[String.fromCharCode(decimal)] = entities[decimal];
        }
    }

    return hash_map;
}
(function () {
    tinymce.create('tinymce.plugins.wpsm', {
        init: function (editor, url) {

            editor.addButton('showsitemap', {
                title: 'Site Map',
                cmd: 'showsitemap',
                image: url + '/../images/sitemap.png'
            });

            editor.addCommand('showsitemap', function (ui, v) {
                $templateurl = ajaxurl + '?action=get_site_map';
                if (typeof v !== 'undefined') {
                    $wpsm_post_id = v.wpsm_post_id;
                    $wpsm_cat = v.wpsm_cat;
                    $wpsm_fmt = v.wpsm_fmt;
                    $wpsm_type = v.wpsm_type;
                    $wpsm_tag = v.wpsm_tag;
                    $wpsm_aut = v.wpsm_aut;
                    $wpsm_depth = v.wpsm_depth;
                    $wpsm_group = v.wpsm_group;
                    $wpsm_link = v.wpsm_link;
                    $wpsm_exclude = v.wpsm_exclude;
                    $wpsm_grouponly = v.wpsm_grouponly;
                    $wpsm_class = v.wpsm_class;
                    $wpsm_id = v.wpsm_id;
                    if ($wpsm_post_id) $templateurl += "&post_id=" + window.encodeURIComponent($wpsm_post_id);
                    if ($wpsm_cat) $templateurl += "&cat=" + window.encodeURIComponent($wpsm_cat);
                    if ($wpsm_fmt) $templateurl += "&fmt=" + window.encodeURIComponent($wpsm_fmt);
                    if ($wpsm_type) $templateurl += "&type=" + window.encodeURIComponent($wpsm_type);
                    if ($wpsm_tag) $templateurl += "&tag=" + window.encodeURIComponent($wpsm_tag);
                    if ($wpsm_aut) $templateurl += "&aut=" + window.encodeURIComponent($wpsm_aut);
                    if ($wpsm_depth) $templateurl += "&depth=" + window.encodeURIComponent($wpsm_depth);
                    if ($wpsm_group) $templateurl += "&group=" + window.encodeURIComponent($wpsm_group);
                    if ($wpsm_link) $templateurl += "&link=" + window.encodeURIComponent($wpsm_link);
                    if ($wpsm_exclude) $templateurl += "&exclude=" + window.encodeURIComponent($wpsm_exclude);
                    if ($wpsm_grouponly) $templateurl += "&grouponly=" + window.encodeURIComponent($wpsm_grouponly);
                    if ($wpsm_class) $templateurl += "&class=" + window.encodeURIComponent($wpsm_class);
                    if ($wpsm_id) $templateurl += "&id=" + window.encodeURIComponent($wpsm_id);
                }

                // Open window
                editor.windowManager.open({
                        title: 'Site Map',
                        url: $templateurl,
                        buttons: [
                            {
                                text: "Insert",
                                onclick: function (e) {
                                    var $code = '[showsitemap';

                                    var $post_id = jQuery('.mce-container-body iframe').contents().find('#post_id').val();
                                    if ($post_id != null && $post_id) $code = $code + ' post_id="' + $post_id + '"';
                                    var $cat = jQuery('.mce-container-body iframe').contents().find('#cat').val();
                                    if ($cat != null && $cat) $code = $code + ' cat="' + $cat + '"';
                                    var $tag = jQuery('.mce-container-body iframe').contents().find('#tag').val();
                                    if ($tag != null && $tag) $code = $code + ' tag="' + $cat + '"';
                                    var $fmt = jQuery('.mce-container-body iframe').contents().find('#fmt').val();
                                    if ($fmt != null && $fmt) $code = $code + ' fmt="' + $fmt + '"';
                                    var $type = jQuery('.mce-container-body iframe').contents().find('#type').val();
                                    if ($type != null && $type) $code = $code + ' type="' + $type + '"';
                                    var $aut = jQuery('.mce-container-body iframe').contents().find('#aut').val();
                                    if ($aut != null && $aut) $code = $code + ' aut="' + $aut + '"';
                                    var $depth = jQuery('.mce-container-body iframe').contents().find('#depth').val();
                                    if ($depth != null) $code = $code + ' depth=' + $depth;
                                    var $group = jQuery('.mce-container-body iframe').contents().find('#group').val();
                                    if ($group != null) $code = $code + ' group=' + $group;
                                    var $link = jQuery('.mce-container-body iframe').contents().find('#link').val();
                                    if ($link != null) $code = $code + ' link="' + htmlentities($link) + '"';
                                    var $exclude = jQuery('.mce-container-body iframe').contents().find('#exclude').prop('checked') ? 1 : 0;
                                    if ($exclude != null) $code = $code + ' exclude=' + $exclude;
                                    var $grouponly = jQuery('.mce-container-body iframe').contents().find('#grouponly').prop('checked') ? 1 : 0;
                                    if ($grouponly != null) $code = $code + ' grouponly=' + $grouponly;
                                    var $class = jQuery('.mce-container-body iframe').contents().find('#class').val();
                                    if ($class != null && $class) $code = $code + ' class="' + $class + '"';
                                    var $id = jQuery('.mce-container-body iframe').contents().find('#id').val();
                                    if ($id != null && $id) $code = $code + ' id="' + $id + '"';
                                    $code = $code + ']';

                                    editor.insertContent($code);
                                    editor.windowManager.close();
                                }
                            },
                            {
                                text: "Cancel",
                                onclick: 'close'
                            }
                        ],
                        height: 600,
                        inline: 1
                    },
                    {
                        plugin_url: url
                    });
            });

            //replace from shortcode to an placeholder image
            editor.on('BeforeSetcontent', function (event) {
                event.content = replaceShortcodes(event.content, url);
            });

            //replace from placeholder image to shortcode
            editor.on('GetContent', function (event) {
                event.content = restoreShortcodes(event.content, url);
            });

            //open popup on placeholder double click
            editor.on('DblClick', function (e) {
                if (e.target.nodeName == 'IMG' && e.target.className.indexOf('wpsm_panel') > -1) {
                    var title = e.target.attributes['data-sh-attr'].value;
                    title = window.decodeURIComponent(title);
                    console.log(title);
                    editor.execCommand('showsitemap', '', {
                        wpsm_post_id: getAttrNoDecode(title, 'post_id'),
                        wpsm_cat: getAttrNoDecode(title, 'cat'),
                        wpsm_fmt: getAttrNoDecode(title, 'fmt'),
                        wpsm_type: getAttrNoDecode(title, 'type'),
                        wpsm_tag: getAttrNoDecode(title, 'tag'),
                        wpsm_aut: getAttrNoDecode(title, 'aut'),
                        wpsm_depth: getAttrNoDecodeNoQuote(title, 'depth'),
                        wpsm_group: getAttrNoDecodeNoQuote(title, 'group'),
                        wpsm_link: getAttrNoDecode(title, 'link'),
                        wpsm_exclude: getAttrNoDecodeNoQuote(title, 'exclude'),
                        wpsm_grouponly: getAttrNoDecodeNoQuote(title, 'grouponly'),
                        wpsm_class: getAttrNoDecode(title, 'class'),
                        wpsm_id: getAttrNoDecode(title, 'id')
                    });
                }
            });
        }
        // ... Hidden code
    });
    // Register plugin
    tinymce.PluginManager.add('wpsm', tinymce.plugins.wpsm);
})();

