define( ['jquery', 'jquery.dynatree'], function( $ ) {
	function makeChildren( parent, deep, level ) {
        var result = [], selector, i, j, headers, a, level, children, next;
        if ( deep > 0 ) {
            level = level || 2;
            selector = 'h' + level;

            headers = $(parent).find( selector );
            for ( i=0; i < headers.length; i++ )
            {
                result[i] = [];
                result[i]['title'] = $(headers[i]).text();
                result[i]['tooltip'] = $(headers[i]).text();
                result[i]['href'] = '#'+headers[i].id;
                result[i]['target'] = '';

                for( j=1; j <= $(headers[i]).parent().children().length; j++ ) {
                    children = makeChildren( $( $(headers[i]).parent().children()[j] ), deep - 1, level + 1 );
                    if ( children.length ) {
                        result[i]['children'] = children;
                    }
                }

                a = document.createElement("a");
                a.name = "" + headers[i].id;
                $(headers[i]).before( a );
            }
        }
        return result;
    };
	
    function initTreeButton( $tree ) {
    	var el;
        $tree.before( '<div id="d_tree_but"><input type="button" id="i_tree_expand" value=" + expand" /><input type="button" id="i_tree_collapse" value=" - collapse" /></div>' );
        el = $( '#i_tree_expand' );
        el.click( function(){
            $tree.dynatree("getRoot").visit(function(node){
                node.expand(true);
            });
        });

        el = $( '#i_tree_collapse' );
        el.click( function(){
            $tree.dynatree("getRoot").visit(function(node){
                node.expand(false);
            });
        });
    };
    
    function initTreeFilter( $tree ) {
        var el, query;
        $tree.before( '<div id="d_search"><p>Фильтр <input type="text" id="i_search" title="Нажмите Enter для применения фильтра" /></p></div>' );
        el = $( '#i_search' );
        $(el).change( function() {
                var query = el.val();
                $container.dynatree('getRoot').visit(function(node) {
                    node.expand(true);
                    if (!query.length) {
                        $(node.li).show();
                    } else if (node.data.title.indexOf(query) != -1) {
                        $(node.li).show();
                        node.visitParents(function(parent) {
                            $(parent.li).show();
                        }, false);
                    } else {
                        $(node.li).hide();
                    }
            });
            }
        );
    };
    
	function tree( $obj, toclevels) {
		var children;
        children = makeChildren( $('#content'), 3 );

        $obj.dynatree({
            onActivate : function(node) {
                var href = node.data.href;
                if( href ){
                    window.location.href = href;
                }
                return true;
            },
            persist : true,
            children : children
        });
	};
	
	function run( $obj, toclevels ) {
		tree( $obj, toclevels );
        initTreeButton( $obj );
        initTreeFilter( $obj );
	}
	
	return {
		run: run
	};
});