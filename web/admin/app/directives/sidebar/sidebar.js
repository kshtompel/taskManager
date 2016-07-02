;(function (angular){
    angular.module('TaskManager')
        .controller('SidebarController', ['$scope', function ($scope) {
        }])
        .directive('sidebar', function () {
            return {
                templateUrl: '/admin/app/directives/sidebar/sidebar.html',
                restrict: 'E',
                replace: true
            }
        });
})(window.angular);