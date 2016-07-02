;(function (angular, window) {
    "use strict";

    var app = angular.module('TaskManager', [
        'tasks.admin.controllers',
        'ui-notification',
        'ui.router',
        'ui.bootstrap',
        'ui.bootstrap.datetimepicker',
        'ui.select',
        'angular-loading-bar',
        'ngAnimate',
        'xeditable',
        'frapontillo.bootstrap-switch',
        'cfp.hotkeys',
        'mwl.calendar',
        'ngSanitize'
    ]);

    app.service('$AdminRequest', ['$http', '$q', function ($http, $q)
    {
        this.request = function (url, method, parameters, headers)
        {
            if (typeof headers == 'undefined') {
                headers = {};
            }

            headers['Content-Type'] = 'application/json';

            var deferred = $q.defer();

            var request = {
                method: 'POST',
                url: url,
                data: JSON.stringify(parameters),
                headers: headers
            };


            $http(request).then(
                function (response) {
                    console.log(response);
                     if (response.data.status) {
                        deferred.resolve(response);
                    } else {
                        deferred.reject(response);
                    }
                },

                function (response) {
                    console.log(response);
                    deferred.reject(response);
                }
            );

            return deferred.promise;
        };
    }]);

    /**
     * Configuration section
     */

    app.config(['cfpLoadingBarProvider', function (cfpLoadingBarProvider) {
        cfpLoadingBarProvider.includeSpinner = false;
    }]);

    app.config(['$urlRouterProvider', function ($urlRouterProvider) {
        $urlRouterProvider.otherwise('/dashboard/home')
    }]);

    app.config(['NotificationProvider', function (NotificationProvider) {
        NotificationProvider.setOptions({
            delay: 10000,
            positionX: 'right',
            positionY: 'bottom'
        });
    }]);

})(window.angular, window);