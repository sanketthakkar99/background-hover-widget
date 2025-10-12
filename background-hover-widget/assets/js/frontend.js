(function($){
    'use strict';
    var bhwInit = function( $scope ) {
        var container = $scope && $scope.length ? $scope.find('.bhw-widget') : $('.bhw-widget');
        container.each(function(){
            var $widget = $(this);
            var $bg = $widget.find('.bhw-bg').first();
            var defaultBg = $bg.css('background-image') || '';
            function extractUrl(str) {
                if (!str) return '';
                return str.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
            }
            var defaultUrl = extractUrl(defaultBg);

            $widget.on('mouseenter', '.bhw-item', function(){
                var $item = $(this);
                var bg = $item.attr('data-bhw-bg') || '';
                if (bg) $bg.css('background-image', 'url("' + bg + '")');
                else $bg.css('background-image', defaultUrl ? 'url("' + defaultUrl + '")' : '');
            });

            $widget.on('mouseleave', function(){
                if (defaultUrl) $bg.css('background-image', 'url("' + defaultUrl + '")');
                else $bg.css('background-image', '');
            });

            $widget.on('focus', '.bhw-item', function(){
                var bg = $(this).attr('data-bhw-bg') || '';
                if (bg) $bg.css('background-image', 'url("' + bg + '")');
            });
            $widget.on('blur', '.bhw-item', function(){
                if (defaultUrl) $bg.css('background-image', 'url("' + defaultUrl + '")');
            });
        });
    };

    $(document).ready(function(){ bhwInit(); });

    if ( window.elementorFrontend ) {
        window.elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function($scope){ bhwInit( $scope ); } );
        window.elementorFrontend.hooks.addAction( 'frontend/element_ready/bhw-background-hover', function($scope){ bhwInit( $scope ); } );
    }

})(jQuery);