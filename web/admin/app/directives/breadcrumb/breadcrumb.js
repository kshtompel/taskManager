;(function (angular) {
    "use strict";

    angular.module('TaskManager')
        .directive('breadrcumb', function () {
            return {
                templateUrl: '/admin/app/directives/breadcrumb/breadcrumb.html',
                restrict: 'E',
                replace: true,
                transclude: true
            }
        });
})(window.angular);