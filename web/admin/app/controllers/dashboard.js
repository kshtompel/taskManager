;(function (angular) {
    "use strict";

    var dashboardModule = angular.module('tasks.admin.controller.dashboard', ['ui.router']);

    dashboardModule
        .config(['$stateProvider', function ($stateProvider){
            $stateProvider
                .state('dashboard', {
                    url: '/dashboard',
                    templateUrl: '/admin/views/dashboard/main.html'
                })
                .state('dashboard.home', {
                    url: '/home',
                    templateUrl: '/admin/views/dashboard/home.html',
                    controller: [
                        '$scope',
                        DashboardController
                    ],
                    pageTitle: 'Dashboard'
                })
        }]);

    function DashboardController ($scope)
    {
        console.log($scope)
    }
})(window.angular);
