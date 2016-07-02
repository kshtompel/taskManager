;(function (angular){
    "use strict";

    angular.module('TaskManager')
        .directive('header', function () {
            return {
                templateUrl: '/admin/app/directives/header/header.html',
                restrict: 'E',
                replace: true
            }
        });
})(window.angular);