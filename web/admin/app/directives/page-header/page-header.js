;(function (angular) {
    "use strict";

    angular.module('TaskManager')
        .directive('pageHeader', function () {
            return {
                templateUrl: '/admin/app/directives/page-header/page-header.html',
                restrict: 'E',
                replace: true,
                transclude: true
            }
        });
})(window.angular);